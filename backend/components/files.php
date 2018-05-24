<?

$files = files::getInstance();
	
class files {
	
    protected static $_instance;
	private function __clone() {}
	private function __wakeup() {}
	private function __construct() {} 
    public static function getInstance() {
        if (self::$_instance === null) { self::$_instance = new self; }
		return self::$_instance;
    }

    //Разрешенные форматы для загрузки файлов для определенной функции
    public function formats($name){

    	$array = array(
    		'images' => array("jpg","jpeg","png","gif"),
			'documents' => array("jpg","jpeg","png","gif","bmp","tif","tiff","ico",
				"doc","docx","pot","pps","ppt","pptx","ppam","pptm",
				"xla","xls","xlsx","xlt","xlw","xlam","xlsb","xlsm","xltm",
				"docm","dotm","pptx","sldx","ppsx","potx","tar","zip","gz","gzip",
				"odt","odp","ods","odg","odc","odb","odf",
				"7z","rar","zip","txt","rtf","RTF","djvu","pdf","epub","cdr","ai")
    	);

    	if(!is_array($array[$name]))return array();
    		return $array[$name];
    }

	//Загрйзка файлов на сервер
	public function uploads ($files,$name,$data=array()){

		$array = array();
		foreach( $files['tmp_name'] as $key => $tmp_name ){

		    $file = array(
		        'name' => $files['name'][$key],
		        'type' => $files['type'][$key],
		        'tmp_name' => $tmp_name,
		        'error' => $files['error'][$key],
		        'size' => $files['size'][$key]
            );

			$array[] = $this->upload($file,$name,$data);
		}

		return $array;
	}
	
	//Загрузка файла на сервер
	public function upload ($file,$name,$data=array()){
		$db = db::getInstance();

		$max_size = 55120; //Максимальный размер в КБ
		$valid_formats = $this->formats($name); //Разрешенные форматы
		$root = $_SERVER['DOCUMENT_ROOT'].'/';
		$time = time();

		if( !is_array($file) )
			return array('status' => 'error','text' => 'Файл не выбран.');

		//Проверяем расширение файла
		$file_name = stripslashes($file['name']);

		$i = strrpos($file_name,".");
		if (!$i) {
			return array(
				'status' => 'error',
				'text' => 'Файл не выбран.'
			);
		}
		$l = strlen($file_name) - $i;
		$ext = strtolower(substr($file_name,$i+1,$l));

		if(in_array($ext,$valid_formats)){
			if(filesize($file['tmp_name']) < $max_size*1024){


					//Создаем нужные папки по датам если нет такой
					$dir_y = $root.'uploads/'.date('Y/',$time);
					if(!is_dir($dir_y)){
						mkdir($dir_y);
					}

					$dir_m = $root.'uploads/'.date('Y/m/',$time);
					if(!is_dir($dir_m)){
						mkdir($dir_m);
					}

					$dir_d = $root.'uploads/'.date('Y/m/d/',$time);
					if(!is_dir($dir_d)){
						mkdir($dir_d);
					}

					$dir_h = $root.'uploads/'.date('Y/m/d/H/',$time);
					if(!is_dir($dir_h)){
						mkdir($dir_h);
					}

					$dir_i = $root.'uploads/'.date('Y/m/d/H/i/',$time);
					if(!is_dir($dir_i)){
						mkdir($dir_i);
					}

				$new_file_name = md5(time().$file['name'].rand()).'.'.$ext;
				$url = '/uploads/'.date('Y/m/d/H/i',$time).'/'.$new_file_name;

				if(move_uploaded_file($file['tmp_name'], $root.'uploads/'.date('Y/m/d/H/i',$time).'/'.$new_file_name)){

					if($file['name'] == ''){
						$file['name'] = md5($url).'.'.$ext;
					}

					$file['name'] = str_replace(" ", "_", $file['name']);
					if(mb_strlen($file['name'],'UTF-8') > 47){
						$file['name'] = mb_substr($file['name'],0,40,'UTF-8').'.'.$ext;
					}
					$file['name'] = htmlspecialchars($file['name']);

						//Если больше 50 симоволов - обрезаем
						if( mb_strlen($file['name'],'UTF-8') > 35 )
							$file['name'] = time().'-'.rand().'.'.$ext;

					/*$insert = array(
						"user_id" => 0,
						"url" => $url,
						"name" => $file['name'],
						"time" => time()
					);
					$bd->query("INSERT INTO `uploads` SET ?u",$insert);*/

						$return = array(
							'status' => 'success',
							'text' => 'Файл успешно загружен.',
							'name' => $file['name'],
							//'id' => $bd->insertId(),
							'url' => $url,
							'type' => $file['type']
						);

				}else{

					$return = array(
						'status' => 'error',
						'text' => 'Произошла ошибка.',
						'name' => $file['name']
					);
				}

			}else{

				$return = array(
					'status' => 'error',
					'text' => 'Максимальный размер загружаемого файла: "'.$file['name'].'" - '.$max_size.' КБ.',
					'name' => $file['name']
				);

			}
		}else{

			$text_formats = '';
			foreach($valid_formats as $format){
				if($text_formats != ''){ $text_formats .= ', '; }
				$text_formats .= '.'.$format;
			}

			$return = array(
				'status' => 'error',
				'text' => 'Ошибка при загрузки файла: "'.$file['name'].'". Разрешенные форматы: '.$text_formats,
				'name' => $file['name']
			);
		}

		$return = array_merge($return,$data);
		return $return;
	}

	//Сформировать загрузку файла
	public function html_upload_file ($name,$multiple=true,$function='main_function',$button='Загрузить файл'){

		$text_formats = '';
		foreach($this->formats($name) as $format){
			if($text_formats)$text_formats .= ', ';
			$text_formats .= '.'.$format;
		}

		$multiple = ($multiple) ? $multiple = 'multiple="multiple"' : $multiple = '';

		$html = '
		<div class="clear"></div>
		<div id="files_'.$name.'">
			<p class="help-block">Разрешенные форматы: '.$text_formats.'</p>
			<form action="/extra/upload_files/'.$name.'" method="POST" target="iframe_'.$name.'" enctype="multipart/form-data">
	        	<input type="file" name="files[]" '.$multiple.' class="files">
	        	<a type="submit" class="btn btn-warning upload_files" href="">'.$button.'</a>
	        </form>
	        <iframe id="iframe_'.$name.'" name="iframe_'.$name.'" class="files_upload '.$function.'"></iframe>
	        <div class="error_form"></div>
	        <div class="return_uploads_files"></div>
	        <div class="hide id_uploads_file"></div>
        </div>
        <div class="clear"></div>
		';

		return $html;
	}


	//Удалить файл
	public function delete_file ($id){
		$bd = bd::getInstance();

		$file = $bd->getRow('SELECT * FROM `uploads` WHERE `id`=?i',$id);
		if($file){
			$bd->query('DELETE FROM `uploads` WHERE `id`=?i',$id);
			unlink($_SERVER['DOCUMENT_ROOT'].$file['url']);
		}
	}

	//Уменьшить изображение
	public function resize($file_input, $w_o, $h_o, $percent = false) {
		list($w_i, $h_i, $type) = getimagesize($file_input);
		if (!$w_i || !$h_i) {
			return 'Невозможно получить длину и ширину изображения';
			return;
	        }
	        $types = array('','gif','jpeg','png');
	        $ext = $types[$type];
	        if ($ext) {
	    	        $func = 'imagecreatefrom'.$ext;
	    	        $img = $func($file_input);
	        } else {
	    	        return 'Некорректный формат файла';
			return;
	        }
		if ($percent) {
			$w_o *= $w_i / 100;
			$h_o *= $h_i / 100;
		}
		if (!$h_o) $h_o = $w_o/($w_i/$h_i);
		if (!$w_o) $w_o = $h_o/($h_i/$w_i);

		if($w_o < 0)
			$w_o = 100;

		if($h_o < 0)
			$h_o = 100;

		$img_o = imagecreatetruecolor($w_o, $h_o);
		imagealphablending($img_o, false);
		imagesavealpha($img_o, true);
		imagecopyresampled($img_o, $img, 0, 0, 0, 0, $w_o, $h_o, $w_i, $h_i);
		if ($type == 2) {
			return imagejpeg($img_o,$file_input,100);
		} else {
			$func = 'image'.$ext;
			return $func($img_o,$file_input);
		}
	}

	//Обрезать изображение
	public function crop($file_input, $crop = 'square',$percent = false) {
		list($w_i, $h_i, $type) = getimagesize($file_input);
		if (!$w_i || !$h_i) {
			return 'Невозможно получить длину и ширину изображения';
	        }
	        $types = array('','gif','jpeg','png');
	        $ext = $types[$type];
	        if ($ext) {
	    	        $func = 'imagecreatefrom'.$ext;
	    	        $img = $func($file_input);
	        } else {
	    	        return 'Некорректный формат файла';
	        }
		if ($crop == 'square') {
			$min = $w_i;
			if ($w_i > $h_i) $min = $h_i;
			$w_o = $h_o = $min;
		} else {
			list($x_o, $y_o, $w_o, $h_o) = $crop;
			if ($percent) {
				$w_o *= $w_i / 100;
				$h_o *= $h_i / 100;
				$x_o *= $w_i / 100;
				$y_o *= $h_i / 100;
			}
	    	        if ($w_o < 0) $w_o += $w_i;
		        $w_o -= $x_o;
		   	if ($h_o < 0) $h_o += $h_i;
			$h_o -= $y_o;
		}

		if($w_o < 0)
			$w_o = 100;

		if($h_o < 0)
			$h_o = 100;

		$img_o = imagecreatetruecolor($w_o, $h_o);
		imagealphablending($img_o, false);
		imagesavealpha($img_o, true);
		imagecopy($img_o, $img, 0, 0, $x_o, $y_o, $w_o, $h_o);
		if ($type == 2) {
			return imagejpeg($img_o,$file_input,100);
		} else {
			$func = 'image'.$ext;
			return $func($img_o,$file_input);
		}
	}

	//Загрузить изображение с определенным размером
	public function upload_img ($file,$width,$height){
		$files = files::getInstance();

		$root = $_SERVER['DOCUMENT_ROOT'];

        //Заливаем файл на сервер
        $response = $files->upload($file,'images');

        if($response['status'] == 'error')
        	return array('status' => 'error','text' => $response['text']);

        //Если все окей
        $img = htmlspecialchars( trim($response['url']) );

        //Формируем массив для обрезки изображения
        if( $width != $height ):

        	//Получить зармер изображения
        	list($w_i, $h_i, $type) = getimagesize($root.$img);

        	//Если ширина у загружаемого изображения больше чем нужно
        	if( $w_i > $width ):

        			$crop_width = $w_i;
        			$crop_height = $w_i/($width/$height);
        			if( $crop_height > $h_i ):
        				$crop_width = $w_i*($height/$width);
        				$crop_height = $h_i;
        			endif;


        	//Если высота изображения больше чем нужно
        	elseif( $h_i > $height):

        			$crop_width = $w_i;
        			$crop_height = $w_i/($width/$height);


        	//Если изображение меньше чем нужно
        	else:

        		//Если ширина больше высоты
        		if( $w_i > $h_i ):

        			$crop_width = $w_i;
        			$crop_height = $w_i/($width/$height);


        		//Если высота больше ширины
        		else:

        			$crop_width = $w_i;
        			$crop_height = $w_i/($width/$height);

        		endif;

        	endif;
        	
        	$crop = array(0,0,$crop_width,$crop_height);

        else:
        	$crop = 'square';
        endif;


  		//Изменяем размер
		$files->crop($root.$img,$crop);
		$files->resize($root.$img, $width, $height);

		return array('status' => 'success','img' => $img );
	}

}

?>