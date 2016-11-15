<?php

     
    function form_mail($sPara, $sAsunto, $sTexto, $sDe) 
    { 
     
        $bHayFicheros = 0; 
        $sCabeceraTexto = ""; 
        $sAdjuntos = ""; 
        $sCuerpo = $sTexto; 
        $sSeparador = uniqid("_Separador-de-datos_"); 
         
        $sCabeceras = "MIME-version: 1.0\n"; 
         
        // Recogemos los campos del formulario 
        foreach ($_POST as $sNombre => $sValor) 
            $sCuerpo = $sCuerpo."\n".$sNombre." = ".$sValor; 
        
        
        // Recorremos los Ficheros 
        foreach ($_FILES as $vAdjunto) 
        { 

            
            
            if ($bHayFicheros == 0) 
            { 
                 
                // Hay ficheros 
                 
                $bHayFicheros = 1; 
                 
                // Cabeceras generales del mail 
                $sCabeceras .= "Content-type: multipart/mixed;"; 
                $sCabeceras .= "boundary=\"".$sSeparador."\"\n"; 
                 
                // Cabeceras del texto 
                $sCabeceraTexto = "--".$sSeparador."\n"; 
                $sCabeceraTexto .= "Content-type: text/plain;charset=iso-8859-1\n"; 
                $sCabeceraTexto .= "Content-transfer-encoding: 7BIT\n\n"; 
                 
                $sCuerpo = $sCabeceraTexto.$sCuerpo; 
                 
            }
            
            
            // Filtramos que sea el archivo que se solicita.
            if ($vAdjunto[type] =="application/pdf") // $_FILES[$vAdjunto][type] =="application/msword"))
                { echo "es pdf";
                 exit();
                }else if ($vAdjunto[type] =="application/msword")
                        {
                            echo "es doc";
                            exit();
                            }else {echo "No es nada";
                                  exit();}
                        
            
            // Compruebo que el archivo seleccionado es del formato PDF o DOC
            /* if (! $vAdjunto[type] =="application/pdf") // $_FILES[$vAdjunto][type] =="application/msword"))
                {
                    echo "No es un archivo válido. Recuerde debe de ser PDF o DOC "; //Me devuelve el valor en bytes
                    $url = htmlspecialchars($_SERVER['HTTP_REFERER']);
                    echo "<br><br><a href='$url'>Volver</a><br><br>";
                    exit(); 
                }*/

            
            if (! ($vAdjunto["size"] > 0 and filesize($vAdjunto["tmp_name"]) < 2000000) )  //No más de 2MB
            {
              echo "<br><br><p align='center'>Es muy grande el archivo. El tamaño es :" .filesize($vAdjunto["tmp_name"]."</p>"); //Me devuelve el valor en bytes
              $url = htmlspecialchars($_SERVER['HTTP_REFERER']);
              echo "<br><br><a align='center' href='$url'>Volver</a><br><br>";
              exit();
                
            }else if ($vAdjunto["size"] > 0 and filesize($vAdjunto["tmp_name"]) < 2000000) // Se añade el fichero 
                    { 
                        $sAdjuntos .= "\n\n--".$sSeparador."\n"; 
                        $sAdjuntos .= "Content-type: ".$vAdjunto["type"].";name=\"".$vAdjunto["name"]."\"\n"; 
                        $sAdjuntos .= "Content-Transfer-Encoding: BASE64\n"; 
                        $sAdjuntos .= "Content-disposition: attachment;filename=\"".$vAdjunto["name"]."\"\n\n";                  

                        $oFichero = fopen($vAdjunto["tmp_name"], 'rb'); 
                        $sContenido = fread($oFichero, filesize($vAdjunto["tmp_name"])); 
                        $sAdjuntos .= chunk_split(base64_encode($sContenido)); 
                        fclose($oFichero); 
                    }
             
        } 
        
        // Si hay ficheros se añaden al cuerpo 
        if ($bHayFicheros) 
            $sCuerpo .= $sAdjuntos."\n\n--".$sSeparador."--\n"; 
         
        // Se añade la cabecera de destinatario 
        if ($sDe)$sCabeceras .= "From:".$sDe."\n"; 
         
        // Por último se envia el mail 
        return(mail($sPara, $sAsunto, $sCuerpo, $sCabeceras)); 
    } 
         
    //Usar llamando a la función form_mail: 
    if (form_mail("receptordelcorreo@gmail.com", 
                "Solicitud de empleo desde la web.", 
                "Los datos introducidos en el formulario son:\n", 
                $_POST["E-mail"])) 
    echo "Su formulario ha sido enviado con exito. Gracias por su interés."; 
        
    /* $url = htmlspecialchars($_SERVER['HTTP_REFERER']);
    echo "<br><br><a href='$url'>Volver</a><br><br>";*/

?>
        <div>
            <!-- Volver apantalla inicial -->
            <br><br>
            <input type="button" value="Volver" onclick="location.href='../index.html'">
            <br><br>
        </div>