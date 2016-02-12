<?php

class Ngseed {
	/* Attrs */
	private $template_dir = '';
	private $base_dir = '';
	private $app_dir = '';
	private $app_name = '';
	private $config_json = 'ng-seed.json';
	private $args = array();
	private $file_types = array("config", "controller", "directive", "service", "factory", "delete");

	/**
	 * Prepare arguments and call app _method or module _method
	 */
	public function exec($args){
		if( count($args) == 0 ){
			$this->exception('Insufficient arguments.');
		}
		$this->args = $args;

		$type = explode(':', $this->args[0]);

		$this->load_app_name();

		if($type[0] == 'app'){
			$this->app_exec();
		} else {
			$this->module_exec();
		}
	}


	/**
	 * Setter and getters
	 */
	public function setTemplate_dir( $value ){
		$this->template_dir = $value;
	}
	public function getTemplate_dir(){
		return $this->template_dir;
	}
	public function setApp_dir( $value ){
		$this->base_dir = $value;
		$this->app_dir  = $this->base_dir . '/app';
	}
	public function getApp_dir(){
		return $this->base_dir;
	}
	


	/**
	 * Hepers and utils
	 */
	private function recursive_delete($src){
		$dir = opendir($src);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				$full = $src . '/' . $file;
				if ( is_dir($full) ) {
					$this->recursive_delete($full);
				}
				else {
					unlink($full);
				}
			}
		}
		closedir($dir);
		rmdir($src);
	}
	private function load_app_name(){
		if(file_exists( $this->base_dir . '/' . $this->config_json )){
			$json = $this->read_json();
			$this->app_name = $json['appname'];
		}
	}
	private function exception($content){
		die('End execution at: ' . date('H:i:s') . "\n{$content}");
	}
	private function create_folder($dir, $mode = "0777"){
		if(!file_exists($dir))
			return mkdir($dir, $mode);

		return true;
	}
	private function create_file($file, $content = "", $mode = "x"){
		$_file = fopen($file, $mode);

		if(!$_file){
			$this->exception("Unable to open file: {$file}");
			return false;
		}

		fwrite($_file, $content);
		fclose($_file);

		return true;
	}
	private function write_json($data){
		$data = is_array($data) ? $this->json_encode_pretty($data) : $data;
		$this->create_file("{$this->base_dir}/{$this->config_json}", $data, 'w');
	}
	private function read_json(){
		return json_decode($this->get_contents( "{$this->base_dir}/{$this->config_json}" ), true);
	}
	private function render($file, $data = array()){
		$template = $this->get_contents( "{$this->template_dir}/{$file}" );
		$pattern = "/\%([a-zA-Z0-9]*?)\%/im";

		preg_match_all($pattern, $template, $matches);
		
		// Prepare data
		$data_keys   = array();
		$data_values = array();
		foreach ($matches[1] as $value) {
			$data_keys[] = '%' . $value . '%';
			$data_values[] = in_array($value, array_keys($data)) ? $data[$value] : '';
		}

		// Replace data
		$rendered = str_replace($data_keys, $data_values, $template);
		
		return $rendered;
	}
	private function get_contents($file){
		return file_get_contents( $file );
	}
	private function json_encode_pretty($data){
		if( defined("JSON_PRETTY_PRINT") )
			return json_encode( $data, JSON_PRETTY_PRINT );

		return json_encode( $data );
	}
	private function update_app(){
		$this->update_injections();
		$this->update_autoload();
	}
	
	


	/**
	 * Methods
	 */
	private function inject($inject){
		// Update injections on config json
		$json_config = $this->read_json();

		if(!isset($json_config["injections"][$inject]))
			$json_config["injections"][] = $inject;

		$this->write_json( $json_config );

		// Update on js file
		$app_file = $this->app_dir . '/app.js';
		$this->update_injections();

	}
	private function update_injections(){
		// Read json
		$json = $this->read_json();

		// Create new array
		$data = array();

		// Add json->injections on array
		$data = array_merge($data, $json['injections']);

		// Iterate json->modules to add modules
		foreach ($json['modules'] as $module) {
			$data[] = $module['name'] . '.' . $module['modulename'];
		}

		// Get template from angular-app.js
		$template = $this->render('angular-app.js', array(
			'appname' => $json['appname'],
			'author' => $json['author'],
			'appinjection' => $this->json_encode_pretty($data)
		));
		
		// Rewrite app.js
		$this->create_file($this->app_dir . '/app.js', $template, "w");
		
		// Update autoload files
		$this->update_autoload();
	}
	private function update_autoload(){
		// Read json
		$json = $this->read_json();

		// Create new array
		$scripts = array(
			'app.js', 'config.js', 'routes.js'
		);

		// Iterate modules and files
		foreach ($json["modules"] as $module) {
			$module_name = $module['modulename'];

			unset($module['name']);
			unset($module['modulename']);
			unset($module['test']);

			foreach ($module as $files) {
				foreach ($files as $file) {
					$scripts[] = $module_name . '/' . $file;
				}
			}
		}

		// Reverse priority
		$scripts = array_reverse($scripts);

		// Make html script
		$_out = "";
		foreach ($scripts as $script) {
			$_out .= '<script type="text/javascript" src="' . $json['base_url'] . 'app/' . $script . '"></script>' . "\n";
		}
		
		// Save with extension
		$this->create_file($this->base_dir . '/' . $json['autoload'] . '.' . $json['extension'], $_out, "w");
	}
	private function create_filter($filter){
		//
	}
	private function module_exists($name){
		return is_dir("{$this->app_dir}/{$name}");
	}
	private function delete_module($name){
		// Read json
		$json = $this->read_json();

		// Unset on json
		foreach ($json['modules'] as $index => $module) {
			if($module['modulename'] == $name){
				unset($json['modules'][$index]);
				continue;
			}
		}

		// Save json
		$this->write_json($json);

		// Remove files and folders
		$this->recursive_delete($this->app_dir . '/' . $name);

		// Update files
		$this->update_app();
	}
	private function create_module($name){
		$module_dir = "{$this->app_dir}/{$name}";

		// Create folder
		$this->create_folder($module_dir);

		// Create view folder
		$this->create_folder("{$module_dir}/views");

		// Create app.js
		$this->create_file("{$module_dir}/app.js", $this->render('app.js', array(
			"appname" => $this->app_name,
			"module"=> $name
		)));

		// Create config-name.js
		$this->create_app_file($name, 'config', $name);
		
		// Create controller-name.js
		$this->create_app_file($name, 'controller', $name);
		
		
		// Update json
		// Read json config
		$json = $this->read_json();

		// Read json module-sample
		$module_json = json_decode($this->get_contents($this->template_dir . '/seed-module.json'), true);
		$module_json['name'] = $this->app_name;
		$module_json['modulename'] = $name;
		$module_json['cofig'] = array("config-{$name}.js");
		$module_json['controller'] = array("controller-{$name}.js");

		// Update json
		$json['modules'][] = $module_json;

		// Save json
		$this->write_json($json);

		return true;
	}
	private function create_app_file($module, $file, $name){
		$data = array(
			"appname" => $this->app_name,
			"module"=> $name,
			"controllername"=> ucfirst($name) . "Ctrl"
		);

		$template = $this->render($file . ".js", $data);
		
		$this->create_file("{$this->app_dir}/{$module}/{$file}-{$name}.js", $template);
		return true;
	}
	


	/**
	 * Executions
	 */
	private function module_exec(){
		$exp = explode(':', $this->args[0]);
		
		$module = $exp[0];
		$file	= $exp[1];
		$name	= @$this->args[1];

		if(!$this->module_exists($module)){
			$this->exception('The module "' . $module . '" no exists.');
		}
		if(!in_array($file, $this->file_types)){
			$this->exception('The file "' . $file . '" is not valid.');
		}
		if($file != "delete"){
			$this->create_app_file($module, $file, $name);
			die('The file "' . $file . '" is created with success on module ' . $module);
		} else {
			$this->delete_module($module);
			die('The module ' . $module . ' is deleted.');
		}
	}
	private function app_exec(){
		$cmd = str_replace('app:', '', $this->args[0]);
		
		switch ($cmd) {
			case 'init': $this->init_method(); break;
			case 'module': $this->module_method(); break;
			case 'update': $this->update_method(); break;
			case 'filter': $this->filter_method(); break;
			case 'set': $this->set_method(); break;
			
			default:
				$this->exception("Invalid argument: app:{$cmd}");
				break;
		}
	}
	private function module_method(){
		if($this->module_exists($this->args[1])){
			$this->exception('Erro: Module "' . $this->args[1] . '" already exist.');
		}
		if($this->args[1] == "filters"){
			$this->exception('Erro: Filters is not valid name.');
		}
		$this->create_module($this->args[1]);
		die('Module "' . $this->args[1] . '" created with success.');
	}
	private function filter_method(){
		if(file_exists( $this->app_dir . '/filters/' . $this->args[1] . '.js' )){
			die('Erro: Filter "' . $this->args[1] . '" already exist.')
		}
		
		$this->create_filter($this->args[1]);
	}
	private function update_method(){
		$this->update_injections();
		$this->update_autoload();
		die('Instalation is completed');
	}
	private function set_method(){
		// Read json
		$json = $this->read_json();

		// Update
		$json[$this->args[1]] = $this->args[2];
		
		// Save
		$this->write_json($json);
		die('The key "' . $this->args[1] . '" updated to "' . $this->args[2] . '"');
	}
	private function init_method(){
		if( file_exists("{$this->base_dir}/{$this->config_json}") ){
			$this->exception("ng-seed file already exists\nMaybe you are searching for the installation\nTry 'app:install'");
		}

		// Create json of app
		$content = $this->get_contents( "{$this->template_dir}/seed.json" );
		$content = json_decode($content, true);

		// Data json
		$content['appname']  = $this->args[1];
		$content['base_url'] = $this->args[2];
		$content['author']   = $this->args[3];
		
		// Create json to manage the app and load name
		$this->create_file("{$this->base_dir}/{$this->config_json}", $this->json_encode_pretty($content));
		$this->load_app_name();

		// Create skeleton
		$this->create_folder($this->app_dir);
		$this->create_folder($this->app_dir . '/filters');

		// Creta basic estructure (app, config, routes)
		$this->create_file("{$this->app_dir}/app.js", $this->render('angular-app.js', $content) );
		$this->create_file("{$this->app_dir}/config.js", $this->render('angular-config.js', $content) );
		$this->create_file("{$this->app_dir}/routes.js", $this->render('angular-routes.js', $content) );

		// Add injections
		$this->inject("{$content["appname"]}.config");
		$this->inject("{$content["appname"]}.routes");

		// Make core module
		$this->create_module('core');
		die('Module "core" created with success');
	}
}
/*

To Do:
- Criar autoload html
- Criar modulo
	- Criar view

*/