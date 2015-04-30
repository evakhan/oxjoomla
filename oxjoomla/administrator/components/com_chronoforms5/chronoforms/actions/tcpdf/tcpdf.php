<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Actions\Tcpdf;
/*** FILE_DIRECT_ACCESS_HEADER ***/
defined("GCORE_SITE") or die;
Class Tcpdf extends \GCore\Admin\Extensions\Chronoforms\Action{
	static $title = 'TCPDF';
	//static $setup = array('simple' => array('title' => 'Data Save'));
	static $group = array('data_management' => 'Data Management');

	var $defaults = array(
		'content' => '',
		'pdf_author' => 'PDF Author.',
		'pdf_title' => 'PDF Title Goes Here.',
		'pdf_subject' => 'Powered by Chronoforms + TCPDF',
		'pdf_keywords' => 'Chronoforms, PDF Plugin, TCPDF, PDF',
		'pdf_file_name' => '',
		'pdf_view' => 'I',
		'pdf_save_path' => '',
		'pdf_post_name' => 'cf_pdf_file',
		'pdf_page_orientation' => 'P',
		'pdf_page_format' => 'A4',
		'pdf_header' => 'Powered by Chronoforms + TCPDF',
		'pdf_header_font' => 'helvetica',
		'pdf_header_font_size' => 10,
		'pdf_footer_font' => 'helvetica',
		'pdf_footer_font_size' => 8,
		'pdf_monospaced_font' => 'courier',
		'pdf_margin_left' => 15,
		'pdf_margin_top' => 27,
		'pdf_margin_right' => 15,
		'pdf_margin_header' => 5,
		'pdf_margin_footer' => 10,
		'pdf_margin_bottom' => 25,
		'pdf_image_scale_ratio' => 1.25,
		'pdf_body_font' => 'courier',
		'pdf_body_font_size' => 14,
		'enable_protection' => 0,
		'permissions' => '',
		'user_pass' => '',
		'owner_pass' => '',
		'sec_mode' => 0,
	);

	function execute(&$form, $action_id){
		$config =  $form->actions_config[$action_id];
		$config = new \GCore\Libs\Parameter($config);
		
		$content = $config->get('content', "");
		ob_start();
		eval('?>'.$content);
		$output = ob_get_clean();
		//if the content box was empty, display the form output
		if(empty($output)){
			$output = $form->form_output;
		}
		$output = \GCore\Libs\Str::replacer($output, $form->data);
		//begin tcpdf code
		require_once('tcpdf/config/lang/eng.php');
		require_once('tcpdf/tcpdf.php');
						
		// create new PDF document
		$pdf = new \TCPDF($config->get('pdf_page_orientation', 'P'), PDF_UNIT, $config->get('pdf_page_format', 'A4'), true, 'UTF-8', false);
		
		//set protection if enabled
		if((bool)$config->get('enable_protection', 0) === true){
			$owner_pass = ($config->get('owner_pass', "") ? $config->get('owner_pass', "") : null);
			$perms = (strlen($config->get('permissions', "")) > 0) ? explode(",", $config->get('permissions', "")) : array();
			$pdf->SetProtection($perms, $config->get('user_pass', ""), $owner_pass, $config->get('sec_mode', ""), $pubkeys=null);
		}

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor($config->get('pdf_author', 'PDF Author.'));
		$pdf->SetTitle($config->get('pdf_title', 'PDF Title Goes Here.'));
		$pdf->SetSubject($config->get('pdf_subject', 'Powered by Chronoforms + TCPDF'));
		$pdf->SetKeywords($config->get('pdf_keywords', 'Chronoforms, PDF Plugin, TCPDF, PDF, '.$form->form['Form']['title']));
		// set default header data'
		if(strlen($config->get('pdf_title')) OR strlen($config->get('pdf_header'))){
			$pdf->SetHeaderData(false, 0, $config->get('pdf_title', 'PDF Title Goes Here.'), $config->get('pdf_header', 'Powered by Chronoforms + TCPDF'));
		}

		// set header and footer fonts
		$pdf->setHeaderFont(Array($config->get('pdf_header_font', 'helvetica'), '', (int)$config->get('pdf_header_font_size', 10)));
		$pdf->setFooterFont(Array($config->get('pdf_footer_font', 'helvetica'), '', (int)$config->get('pdf_footer_font_size', 8)));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont($config->get('pdf_monospaced_font', 'courier'));

		//set margins
		$pdf->SetMargins($config->get('pdf_margin_left', 15), $config->get('pdf_margin_top', 27), $config->get('pdf_margin_right', 15));
		$pdf->SetHeaderMargin($config->get('pdf_margin_header', 5));
		$pdf->SetFooterMargin($config->get('pdf_margin_footer', 10));

		//set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, $config->get('pdf_margin_bottom', 25));

		//set image scale factor
		$pdf->setImageScale($config->get('pdf_image_scale_ratio', 1.25));

		//set some language-dependent strings
		$pdf->setLanguageArray($l);

		// ---------------------------------------------------------

		// set font
		$pdf->SetFont($config->get('pdf_body_font', 'courier'), '', (int)$config->get('pdf_body_font_size', 14));

		// add a page
		$pdf->AddPage();
		// create some HTML content
		$css = "";
		
		$output = $css.$output;
		$html = $output;
		// output the HTML content
		$pdf->writeHTML($html, true, false, true, false, '');
		// reset pointer to the last page
		$pdf->lastPage();
		//Close and output PDF document
		if(isset($form->data['pdf_file_name']) && !empty($form->data['pdf_file_name'])){
			$PDF_file_name = $form->data['pdf_file_name'];
		}else{
			if(strlen(trim($config->get('pdf_file_name', ''))) > 0){
				$PDF_file_name = trim($config->get('pdf_file_name', ''))."_".date('YmdHis');
			}else{
				$PDF_file_name = $form->form['Form']['title']."_".date('YmdHis');
			}
		}
		$PDF_view = $config->get('pdf_view', 'I');
		if(($PDF_view == 'F') || ($PDF_view == 'FI') || ($PDF_view == 'FD')){
			jimport('joomla.utilities.error');
			jimport('joomla.filesystem.file');
			jimport('joomla.filesystem.folder');
			$upload_path = $config->get('pdf_save_path');
			if(!empty($upload_path)){
				$upload_path = str_replace(array("/", "\\"), DS, $upload_path);
				if(substr($upload_path, -1) == DS){
					$upload_path = substr_replace($upload_path, '', -1);
				}
				$upload_path = $upload_path.DS;
				$config->set('pdf_save_path', $upload_path);
			}else{
				$upload_path = \GCore\C::ext_path('chronoforms', 'front').'pdfs'.DS.$form->form['Form']['title'].DS;
			}
			//check the save files path is ok
			if(!file_exists($upload_path.DS.'index.html')){
				if(!\GCore\Libs\Folder::create($upload_path)){
					$form->errors[] = "Couldn't create upload directory: ".$upload_path;
					$this->events['fail'] = 1;
					return;
				}
				$dummy_c = '<html><body bgcolor="#ffffff"></body></html>';
				if(!\GCore\Libs\File::write($upload_path.DS.'index.html', $dummy_c)){
					$form->errors[] = "Couldn't create upload directory index file.";
					$this->events['fail'] = 1;
					return;
				}
			}
			
			$PDF_file_path = $upload_path.$PDF_file_name.".pdf";
			$pdf->Output($PDF_file_path, $PDF_view);
			
			//Try to generate an auto file link
			$site_link = \GCore\C::get('GCORE_FRONT_URL');
			if(substr($site_link, -1) == "/"){
				$site_link = substr_replace($site_link, '', -1);
			}
			
			if(strlen(trim($config->get('pdf_post_name', ''))) > 0){
				$form->files[trim($config->get('pdf_post_name', ''))] = array('name' => $PDF_file_name.".pdf", 'path' => $PDF_file_path, 'size' => 0);
				$form->files[trim($config->get('pdf_post_name', ''))]['link'] = str_replace(array(\GCore\C::get('GCORE_FRONT_PATH'), DS), array($site_link, "/"), $upload_path.$PDF_file_name.".pdf");
				$form->data[trim($config->get('pdf_post_name', ''))] = $PDF_file_name.".pdf";
				$form->debug[$action_id][self::$title][] = $PDF_file_path.' has been saved correctly.';
			}
		}else{
			$pdf->Output($PDF_file_name.".pdf", $PDF_view);
		}
		if($PDF_view != 'F'){
			@flush();
			@ob_flush();
			exit;
		}
	}

	public static function config(){
		$fonts = array('courier' => 'courier', 'helvetica' => 'helvetica', 'times' => 'times');
		echo \GCore\Helpers\Html::formStart('action_config tcpdf_action_config', 'tcpdf_action_config_{N}');
		?>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#basic-{N}" data-g-toggle="tab"><?php echo l_('CF_BASIC'); ?></a></li>
			<li><a href="#advanced-{N}" data-g-toggle="tab"><?php echo l_('CF_ADVANCED'); ?></a></li>
			<li><a href="#encryption-{N}" data-g-toggle="tab"><?php echo l_('CF_ENCRYPTION'); ?></a></li>
		</ul>
		<div class="tab-content">
			<div id="basic-{N}" class="tab-pane active">
			<?php
			echo \GCore\Helpers\Html::formSecStart();
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][pdf_author]', array('type' => 'text', 'label' => "Document author", 'class' => 'L', 'sublabel' => ""));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][pdf_title]', array('type' => 'text', 'label' => "Document title", 'class' => 'L', 'sublabel' => ""));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][pdf_subject]', array('type' => 'text', 'label' => "Document subject", 'class' => 'L', 'sublabel' => ""));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][pdf_keywords]', array('type' => 'text', 'label' => "Document keywords", 'class' => 'L', 'sublabel' => ""));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][pdf_file_name]', array('type' => 'text', 'label' => "Document file name", 'class' => 'L', 'sublabel' => ""));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][pdf_view]', array('type' => 'dropdown', 'label' => "View", 'options' => array('I' => 'Display inline', 'F' => 'Save to server', 'D' => 'Download', 'FI' => 'Save + Display inline', 'FD' => 'Save + Download', 'S' => 'String'), 'sublabel' => ""));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][pdf_save_path]', array('type' => 'text', 'label' => "Save path", 'class' => 'XL', 'sublabel' => ""));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][pdf_post_name]', array('type' => 'text', 'label' => "File name in Data/Files array", 'sublabel' => "If your PDF is saved to server then you can use this setting to attach the file to an email."));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][pdf_page_orientation]', array('type' => 'dropdown', 'label' => "Orientation", 'options' => array('P' => 'Portrait', 'L' => 'Landscape'), 'sublabel' => ""));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][pdf_page_format]', array('type' => 'text', 'label' => "Format", 'sublabel' => ""));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][pdf_header]', array('type' => 'text', 'label' => "Document header", 'class' => 'L', 'sublabel' => ""));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][content]', array('type' => 'textarea', 'label' => "Contents", 'rows' => 15, 'cols' => 70, 'sublabel' => "Insert your PDF contents code here, you can use PHP code or fields names between curly brackets, or leave empty to use the form's output buffer data."));
			echo \GCore\Helpers\Html::formSecEnd();
			?>
			</div>
			<div id="advanced-{N}" class="tab-pane">
			<?php
			echo \GCore\Helpers\Html::formSecStart();
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][pdf_header_font]', array('type' => 'dropdown', 'label' => "Header font", 'options' => $fonts, 'sublabel' => ""));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][pdf_header_font_size]', array('type' => 'text', 'label' => "Header font size", 'sublabel' => ""));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][pdf_footer_font]', array('type' => 'dropdown', 'label' => "Footer font", 'options' => $fonts, 'sublabel' => ""));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][pdf_footer_font_size]', array('type' => 'text', 'label' => "Footer font size", 'sublabel' => ""));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][pdf_monospaced_font]', array('type' => 'dropdown', 'label' => "Monospaced Font", 'options' => $fonts, 'sublabel' => ""));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][pdf_margin_left]', array('type' => 'text', 'label' => "Margin left", 'sublabel' => ""));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][pdf_margin_top]', array('type' => 'text', 'label' => "Margin top", 'sublabel' => ""));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][pdf_margin_right]', array('type' => 'text', 'label' => "Margin right", 'sublabel' => ""));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][pdf_margin_header]', array('type' => 'text', 'label' => "Margin header", 'sublabel' => ""));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][pdf_margin_footer]', array('type' => 'text', 'label' => "Margin footer", 'sublabel' => ""));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][pdf_margin_bottom]', array('type' => 'text', 'label' => "Margin bottom", 'sublabel' => ""));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][pdf_image_scale_ratio]', array('type' => 'text', 'label' => "Image scale ratio", 'sublabel' => "ratio used to adjust the conversion of pixels to user units"));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][pdf_body_font]', array('type' => 'dropdown', 'label' => "Body font", 'options' => $fonts, 'sublabel' => ""));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][pdf_body_font_size]', array('type' => 'text', 'label' => "Body font size", 'sublabel' => ""));
			echo \GCore\Helpers\Html::formSecEnd();
			?>
			</div>
			<div id="encryption-{N}" class="tab-pane">
			<?php
			echo \GCore\Helpers\Html::formSecStart();
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][enable_protection]', array('type' => 'dropdown', 'label' => "Document protection", 'options' => array(0 => l_('NO'), 1 => l_('YES'))));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][sec_mode]', array('type' => 'dropdown', 'label' => "Encryption mode", 'options' => array("RSA 40 Bit", "RSA 128 Bit", "AES 128 Bit", "AES 256 Bit")));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][permissions][]', array('type' => 'dropdown', 'label' => "Permissions", "size" => 8, 'options' => array("print" => "print", "modify" => "modify", "copy" => "copy", "extract" => "extract", "assemble" => "assemble", "fill-forms" => "fill-forms"), 'multiple' => 'multiple', 'sublabel' => "Permissions given upon entering the user password."));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][user_pass]', array('type' => 'text', 'label' => "User password", 'sublabel' => "The password required by the user to gain the permissions selected above."));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][{N}][owner_pass]', array('type' => 'text', 'label' => "Owner password", 'sublabel' => "Full permissions password."));
			echo \GCore\Helpers\Html::formSecEnd();
			?>
			</div>
		</div>
			<?php
		echo \GCore\Helpers\Html::formEnd();
	}
}