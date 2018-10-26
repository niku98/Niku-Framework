<?php
namespace System\View;
use AppException;

/**
 *
 */
class NikuTemplate
{
	private $content = '';
	private $extended = null;
	private $stack = [];
	private $included = [];
	private $sections = [];
	private $savedPath = '';
	private $for_include;

	function __construct($path, $for_include = false)
	{
		$this->content = file_get_contents($path);
		$this->for_include = $for_include;
		return $this;
	}

	public function convert()
	{
		$this->removeCommented();
		$this->getLastTemplate();

		$this->convertPHPStatement();
		$this->convertControlStatements();
		$this->convertLoopSatements();
		$this->convertOutputSatements();

		return $this;
	}

	public function getContent()
	{
		return $this->content;
	}

	public function save()
	{
		$folder = root_path.'storage/views/';
		$file = rand_token(12).'.php';

		if(!is_dir($folder)){
			mkdir(root_path.'storage');
			mkdir(root_path.'storage/views');
		}
		file_put_contents($folder.$file, $this->content);
		$this->savedPath = $folder.$file;

		return $this;
	}

	public function getSavedPath()
	{
		return $this->savedPath;
	}

	private function replaceContentWithRegex($regex, $replacement, $multiline = false)
	{
		$regex = $multiline ? '/'.$regex.'/s' : '/'.$regex.'/';
		try {
			$this->content = preg_replace($regex, $replacement, $this->content);
		} catch (\Exception $e) {
			var_dump($regex, $replacement); exit;
		}

		return $this;
	}

	private function findWithRegex($regex, $find_in)
	{
		preg_match_all($regex, $this->$find_in, $result);
		return $result;
	}


	/*
	* PROCESS TEMPALTE
	*/

	private function getLastTemplate()
	{
		$this->getExtendTemplate();
		$this->getIncludedTemplate();
		$this->getSectionTemplate();
		$this->getPushedStack();

		$this->mergeAllTemplate();
	}

	private function getExtendTemplate()
	{
		$paths = $this->findWithRegex('/@extends\(\'(.*)\'\)/', 'content');
		if(count($paths[1]) === 1){
			$path = $paths[1][0];
			$path = root_path.'resources/views/'.$path.'.niku.php';
			if(!file_exists($path)){
				throw new AppException('NikuTemplate with path ['.$path.'] is not exists!');
			}

			$this->extended = (new NikuTemplate($path, true))->convert()->getContent();
		}
	}

	private function getIncludedTemplate()
	{
		$paths = $this->findWithRegex('/@include\(\'(.*)\'\)/', 'content');
		if(count($paths[1]) > 0){
			$file_paths = $paths[1];
			foreach ($file_paths as $file_path) {
				$path = root_path.'resources/views/'.$file_path.'.niku.php';
				if(!file_exists($path)){
					throw new AppException('NikuTemplate with path ['.$path.'] is not exists!');
				}

				$this->included[$file_path] = (new NikuTemplate($path, true))->convert()->getContent();
			}
		}
	}

	private function getSectionTemplate()
	{
		//Type 1
		$paths = $this->findWithRegex('/@section\( ?\'(.*?)\' ?, ?\'(.*)\' ?\)/', 'content');
		if(count($paths[1]) > 0){
			$yeld_names = $paths[1];
			foreach ($yeld_names as $k => $yeld_name) {
				$content = $paths[2][$k];
				$this->sections[$yeld_name] = $content;
				$yeld_name = str_replace('/', '\/', $yeld_name);
				$this->replaceContentWithRegex('@section\(\''.$yeld_name.'\', ?\'(.*?)\' ?\)', '', true);
			}
		}

		//Type 2
		$paths = $this->findWithRegex('/@section\(\'(.*?)\'\)(.*?)@endsection/s', 'content');
		if(count($paths[1]) > 0){
			$yeld_names = $paths[1];
			foreach ($yeld_names as $k => $yeld_name) {
				$content = $paths[2][$k];
				$this->sections[$yeld_name] = $content;
				$yeld_name = str_replace('/', '\/', $yeld_name);
				$this->replaceContentWithRegex('@section\(\''.$yeld_name.'\'\)(.*?)@endsection', '', true);
			}
		}
	}

	private function getPushedStack()
	{
		$paths = $this->findWithRegex('/@push\(\'(.*?)\'\)(.*?)@endpush/s', 'content');
		if(count($paths[1]) > 0){
			$stack_names = $paths[1];
			foreach ($stack_names as $k => $stack_name) {
				$content = $paths[2][$k];
				if(empty($this->stack[$stack_name])){
					$this->stack[$stack_name] = array();
				}
				$this->stack[$stack_name][] = $content;
				$stack_name = str_replace('/', '\/', $stack_name);
				$this->replaceContentWithRegex('@push\(\''.$stack_name.'\'\)(.*?)@endpush', '', true);
			}
		}
	}

	private function mergeAllTemplate()
	{
		if($this->extended !== null){
			$this->content = $this->extended;
		}

		$this->replaceIncluded();
		$this->replaceYeldWithSection();
		$this->pushToStack();
	}

	private function replaceIncluded()
	{
		foreach ($this->included as $name => $content ) {
			$name = str_replace('/', '\/', $name);
			$regex = "@include\('$name'\)";
			$this->replaceContentWithRegex($regex, $content);
		}
	}

	private function replaceYeldWithSection()
	{
		foreach ($this->sections as $yeld_name => $content) {
			$yeld_name = str_replace('/', '\/', $yeld_name);
			$regex = "@yield\('$yeld_name'\)";
			$this->replaceContentWithRegex($regex, $content);
		}

		if(!$this->for_include){
			$this->replaceContentWithRegex("@yield\('(.*?)'\)", '');
		}
	}

	private function pushToStack()
	{
		foreach ($this->stack as $name => $pushes) {
			$replacement = '';
			foreach ($pushes as $push) {
				$replacement .= $push;
			}
			$name = str_replace('/', '\/', $name);
			$regex = "@stack\('$name'\)";
			$this->replaceContentWithRegex($regex, $replacement);
		}

		if(!$this->for_include){
			$this->replaceContentWithRegex("@stack\('(.*?)'\)", '');
		}
	}

	/*
	* CONVERT SATEMENTS
	*/
	private function removeCommented()
	{
		$this->replaceContentWithRegex('{{--(.*?)--}}', '', true);
	}

	private function convertPHPStatement()
	{
		/*Open*/
		$regex = '@php';
		$replacement = '<?php';
		$this->replaceContentWithRegex($regex, $replacement);

		/*End*/
		$regex = '@endphp';
		$replacement = '?>';
		$this->replaceContentWithRegex($regex, $replacement);
	}

	private function convertControlStatements()
	{
		$this->convertIfStatement();
		$this->convertSwitchCaseStatement();
	}

	private function convertIfStatement()
	{
		/*Open*/
		$regex = '@if ?\((.*)\)';
		$replacement = '<?php if($1): ?>';
		$this->replaceContentWithRegex($regex, $replacement);

		$regex = '@elseif ?\((.*)\)';
		$replacement = '<?php elseif($1): ?>';
		$this->replaceContentWithRegex($regex, $replacement);

		/*End*/
		$regex = '@else';
		$replacement = '<?php else: ?>';
		$this->replaceContentWithRegex($regex, $replacement);

		/*End*/
		$regex = '@endif';
		$replacement = '<?php endif; ?>';
		$this->replaceContentWithRegex($regex, $replacement);
	}

	private function convertSwitchCaseStatement()
	{
		/*Open*/
		$regex = '@switch ?\((.*)\)';
		$replacement = '<?php switch($1){ ?>';
		$this->replaceContentWithRegex($regex, $replacement);

		/*Case*/
		$regex = '@case ?\((.*)\)';
		$replacement = '<?php case $1: ?>';
		$this->replaceContentWithRegex($regex, $replacement);

		/*default*/
		$regex = '@default';
		$replacement = '<?php default: ?>';
		$this->replaceContentWithRegex($regex, $replacement);

		/*End*/
		$regex = '@endswitch';
		$replacement = '<?php } ?>';
		$this->replaceContentWithRegex($regex, $replacement);
	}






	private function convertLoopSatements()
	{
		$this->convertForEachSatement();
		$this->convertForSatement();
		$this->convertWhileSatement();
	}

	private function convertForSatement()
	{
		/*Open*/
		$regex = '@for ?\((.*)\)';
		$replacement = '<?php for($1){ ?>';
		$this->replaceContentWithRegex($regex, $replacement);

		/*End*/
		$regex = '@endfor';
		$replacement = '<?php } ?>';
		$this->replaceContentWithRegex($regex, $replacement);
	}

	private function convertForEachSatement()
	{
		/*Open*/
		$regex = '@foreach ?\((.*)\)';
		$replacement = '<?php foreach($1){ ?>';
		$this->replaceContentWithRegex($regex, $replacement);

		/*End*/
		$regex = '@endforeach';
		$replacement = '<?php } ?>';
		$this->replaceContentWithRegex($regex, $replacement);
	}

	private function convertWhileSatement()
	{
		/*Open*/
		$regex = '@while ?\((.*)\)';
		$replacement = '<?php while($1){ ?>';
		$this->replaceContentWithRegex($regex, $replacement);

		/*End*/
		$regex = '@endwhile';
		$replacement = '<?php } ?>';
		$this->replaceContentWithRegex($regex, $replacement);
	}






	private function convertOutputSatements()
	{
		$this->convertWithSpecialCharOutput();
		$this->convertWithRawCharOutput();
	}

	private function convertWithRawCharOutput()
	{
		/*Open*/
		$regex = '{!!(.*?)!!}';
		$replacement = '<?php echo $1; ?>';
		$this->replaceContentWithRegex($regex, $replacement);
	}

	private function convertWithSpecialCharOutput()
	{
		/*Open*/
		$regex = '{{(.*?)}}';
		$replacement = '<?php echo htmlspecialchars($1); ?>';
		$this->replaceContentWithRegex($regex, $replacement);
	}
}


 ?>
