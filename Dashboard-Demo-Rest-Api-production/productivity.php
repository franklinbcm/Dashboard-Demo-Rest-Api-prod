<?php
/*Cabeceras HTTP en PHP para permitir el acceso CORS con Apache o con otro servidor web. 
  Con estas cabeceras no tendremos problemas con el CORS
*/
 
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json;charset=utf-8');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
    die();
}

require_once 'vendor/autoload.php';

$app = new \Slim\Slim();

/*URL para consumir este metodo: http://localhost:8083/Management-Solution-Rest-Api/index.php/pruebas */
/*Conexion a la base de datos */
$db = new mysqli('localhost','fcaamano','fcaamano123','management_solution'); //Conexion a la db
mysqli_set_charset($db, "utf8");// Ayuda a que se pueda utilizar la ñ y acentos dentro de la data que consultemos en base de datos
$app->get("/pruebas",function() use($app, $db){
    echo "Hola mundo desde Slim en PHP";
    var_dump($db);  /*Prueba de conexion a la base de datos */
});

/*Crear un Metodo Post*/
/*URL para consumir este metodo: http://localhost:8083/Management-Solution-Rest-Api/index.php/products */
/*Parametros a enviar:  {"CompanyName":"Productora de Ropas CXA", "Employee":"Manuel Rivera", "Department":"Ventas","EmployeePosition":"Vendedor","EmployeeWorkday":"6","Process":"Completado de Solicitud","SubProcess":"Obtiencion de datos y Envío de Producto","TimeElapsed":"00:05:20"  }*/
$app->post("/productivity",function() use($app, $db){
     $json = $app->request->post('json');
     $data =json_decode( $json ,true); /*decodificar el json*/
    /* var_dump($json); */
    /* var_dump($data); */
    if(!isset($data['CompanyName'])) {/*Si no existe => !isset() */
        $data['CompanyName'] = null;
    };

    if(!isset($data['Employee'])) {/*Si no existe => !isset() */
        $data['Employee'] = null;
    };

    if(!isset($data['Department'])) {/*Si no existe => !isset() */
        $data['Department'] = null;
    };

    if(!isset($data['EmployeePosition'])) {/*Si no existe => !isset() */
        $data['EmployeePosition'] = null;
    };
    if(!isset($data['EmployeeWorkday'])) {/*Si no existe => !isset() */
        $data['EmployeeWorkday'] = null;
    };
    if(!isset($data['Process'])) {/*Si no existe => !isset() */
        $data['Process'] = null;
    };
    if(!isset($data['SubProcess'])) {/*Si no existe => !isset() */
        $data['SubProcess'] = null;
    };
    if(!isset($data['TimeElapsed'])) {/*Si no existe => !isset() */
        $data['TimeElapsed'] = null;
    };

    /*GUARDAR productivitybycompany Query*/
    $query = "INSERT INTO productivitybycompany VALUES(NULL,".
        "'{$data['CompanyName']}',".
        "'{$data['Employee']}',".
        "'{$data['Department']}',".
        "'{$data['EmployeePosition']}',".
        "'{$data['EmployeeWorkday']}',".
        "'{$data['Process']}',".
        "'{$data['SubProcess']}',".
        "'{$data['TimeElapsed']}'".
    ");";

 
    /*Ejecutar en base de datos para GUARDAR el producto*/
    $insert = $db->query($query);  

    $result = array(
        'status'=> 'error',
        'code'=> '404',
        'message'=> 'Fail'
    );

    if($insert){ /*Validar si se inserto el Registro*/
        $result = array(
            'status'=> 'success',
            'code'=> '200',
            'message'=> 'Ok'
        );
    } 
    // if(!$insert){ /*Validar si se inserto el Registro*/
    //     //echo json_last_error($result); /*Imprimir resultado */
    //     //var_dump($data);
    // } 
    

    echo json_encode($result); /*Imprimir resultado */


});


  /*SELECT ALL PRODUCTO Query*/
$app->get("/productivity",function() use($db, $app){
 
   

    $sql ='SELECT * FROM productivitybycompany ORDER BY Id DESC;';
    $query = $db->query($sql); 
    $productList = array();
    
    while($product = $query->fetch_assoc())
    {
        //var_dump($product); 
        $productList [] = $product; 

    }
    //var_dump($productList);  /*Prueba de conexion a la base de datos */
    $result = array(
        'status'=> 'success',
        'code'=> '200',
        'data'=> $productList
    );
    //var_dump($result);  /*Prueba de conexion a la base de datos */
    //echo json_last_error($result); /*devuelve objeto json con el resultado */
    echo json_encode($result, JSON_UNESCAPED_UNICODE); /*devuelve objeto json con el resultado */
});



  /*SELECT by ID PRODUCTO Query URL: http://localhost:8083/Management-Solution-Rest-Api/index.php/productivity/4 */
  $app->get("/productivity/:Id",function($Id) use($db, $app){

    $sql ='SELECT * FROM productivitybycompany WHERE Id ='.$Id;
    $query = $db->query($sql); 
    $result = array(
        'status'=> 'error',
        'code'=> '404',
        'message'=> 'No data to display'
    );

    if($query->num_rows == 1){
        $product = $query->fetch_assoc();
        $result = array(
            'status'=> 'success',
            'code'=> '200',
            'data'=> $product
        );
    } 
 
    echo json_encode($result, JSON_UNESCAPED_UNICODE); /*devuelve objeto json con el resultado */

});

  /*DELETE by ID PRODUCTO Query URL: http://localhost:8083/Management-Solution-Rest-Api/index.php/delete-products/6 */
  $app->get("/delete-productivity/:Id",function($Id) use($db, $app){

    $sql ='DELETE FROM productivitybycompany WHERE Id ='.$Id;
    $query = $db->query($sql); 
    $result = array(
        'status'=> 'error',
        'code'=> '404',
        'message'=> 'Fail'
    );
    if($query){
        $result = array(
            'status'=> 'success',
            'code'=> '200',
            'message'=> 'Ok'
        );
    };


    echo json_encode($result); /*devuelve objeto json con el resultado */

});

  /*UPDATE by ID PRODUCTO Query URL: http://localhost:8083/Management-Solution-Rest-Api/index.php/update-products/6 */
  /*Parameters:   {"ProductName":"Laptop", "Descriptions":"Nueva Laptop", "Price":"1523","Images":"No Images to display"  } */
  $app->post("/update-productivity/:Id",function($Id) use($db, $app){
    $json = $app->request->post('json');
    $data =json_decode( $json ,true); /*decodificar el json*/
    
    if(!isset($data['CompanyName'])) {/*Si no existe => !isset() */
        $data['CompanyName'] = null;
    };

    if(!isset($data['Employee'])) {/*Si no existe => !isset() */
        $data['Employee'] = null;
    };

    if(!isset($data['Department'])) {/*Si no existe => !isset() */
        $data['Department'] = null;
    };

    if(!isset($data['EmployeePosition'])) {/*Si no existe => !isset() */
        $data['EmployeePosition'] = null;
    };
    if(!isset($data['EmployeeWorkday'])) {/*Si no existe => !isset() */
        $data['EmployeeWorkday'] = null;
    };
    if(!isset($data['Process'])) {/*Si no existe => !isset() */
        $data['Process'] = null;
    };
    if(!isset($data['SubProcess'])) {/*Si no existe => !isset() */
        $data['SubProcess'] = null;
    };
    if(!isset($data['TimeElapsed'])) {/*Si no existe => !isset() */
        $data['TimeElapsed'] = null;
    };



   /*UPDATE PRODUCTO Query*/
   $sql = "UPDATE productivitybycompany SET  ".
       "CompanyName = '{$data['CompanyName']}',".
       "Employee = '{$data['Employee']}',".
       "Department = '{$data['Department']}',".
       "EmployeePosition = '{$data['EmployeePosition']}',".
       "EmployeeWorkday = '{$data['EmployeeWorkday']}',".
       "Process = '{$data['Process']}',".
       "SubProcess = '{$data['SubProcess']}', ";

    // if(isset($data['Images'])) {/*Si no existe => !isset() */
    //     $sql.="Images = '{$data['Images']}', ";
    // }

    $sql.="TimeElapsed = '{$data['TimeElapsed']}'  WHERE Id = {$Id}";

    $query = $db->query($sql); 
    $result = array(
        'status'=> 'error',
        'code'=> '404',
        'message'=> 'Fail'
    );

    if($query){
        $result = array(
            'status'=> 'success',
            'code'=> '200',
            'message'=> 'Ok'
        );
    };


    echo json_encode($result); /*devuelve objeto json con el resultado */

});



 /*INSERT IMAGES FILE by ID PRODUCTO Query URL: http://localhost:8083/Management-Solution-Rest-Api/index.php/update-products/6 */
  /*Parameters:   {"ProductName":"Laptop", "Descriptions":"Nueva Laptop", "Price":"1523","Images":"No Images to display"  } */
  $app->post("/upload-file",function() use($db, $app){
    $result = array(
        'status'=> 'error',
        'code'=> '404',
        'message'=> 'The file cannot be loaded'
    );
    
   if(isset($_FILES['uploads'])) {/*Si existe => isset() */
        $piramideUploader = new PiramideUploader();
        $upload = $piramideUploader->upload('image', 'uploads', 'uploads', array('image/jpeg','image/png','image/gif')); //Solo permite archivos con las extensiones que se estan enviando
        $file = $piramideUploader->getInfoFile();
        $file_name = $file['complete_name'];

        if(isset($upload) &&  $upload['uploaded']  == true){
            $result = array(
                'status'=> 'success',
                'code'=> '200',
                'message'=> 'Ok',
                'filename'=> $file_name 
            );
        } 

        
   } 

   echo json_encode($result); /*devuelve objeto json con el resultado */
 
    

});


/*Correr la Api*/
$app->run();