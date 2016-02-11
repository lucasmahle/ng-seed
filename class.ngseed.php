<?php

class Ngseed {
	/* Attrs */
	private $template_dir = '';
	private $app_dir = '';
	private $args = array();

	public function exec($args){
		if( count($args) == 0 ){
			$this->exception('Insufficient arguments.');
		}
		$this->args = $args;

		$type = explode(':', $this->args[0]);

		if($type[0] == 'app'){
			$this->appExec();
		} else {
			$this->moduleExec();
		}
	}

	public function setTemplate_dir( $value ){
		$this->template_dir = $value;
	}
	public function getTemplate_dir(){
		return $this->template_dir;
	}
	public function setApp_dir( $value ){
		$this->app_dir = $value;
	}
	public function getApp_dir(){
		return $this->app_dir;
	}

	private function appExec(){
		$cmd = str_replace('app:', '', $this->args[0]);
		
		switch ($cmd) {
			case 'init': $this->initMethod(); break;
			case 'name': $this->nameMethod(); break;
			case 'module': $this->moduleMethod(); break;
			case 'install': $this->installMethod(); break;
			case 'view-type': $this->view_typeMethod(); break;
			
			default:
				$this->exception('Invalid argument: app:' . $cmd);
				break;
		}
	}

	private function initMethod(){
		if( file_exists($this->app_dir . '/ng-seed.json') ){
			$this->exception("ng-seed file already exists\nMaybe you are searching for the installation\nTry 'app:install'");
		}

		$content = file_get_contents( $this->template_dir . '/seed.json' );
	}
}