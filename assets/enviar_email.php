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
            
            // Lo hemos filtrado la extensión desde html
            
            /*
            if (!($vAdjunto[type] =="application/pdf" OR $vAdjunto[type] =="application/msword")) 
                {
                    echo "<br><br><p align='center'>Debe de seleccionar un archivo correcto :".$vAdjunto[type]."</p>"; //Me devuelve el valor en bytes
                    $url = htmlspecialchars($_SERVER['HTTP_REFERER']);
                    echo "<br><br><a align='center' href='$url'>Volver</a><br><br>";
                    exit();
                }
            */
            
            
            
            
            if (($vAdjunto["size"] > 0 and filesize($vAdjunto["tmp_name"]) > 2000000) )  //No más de 2MB
            {
                echo "<br><br><p align='center'>El archivo que quiere mandar tiene más de 2MB. Vuelva a intentarlo por favor.".filesize($vAdjunto["size"]."</p><br><br>"); //Me devuelve el valor en bytes
                ?>
                    <div align="center">
                        <!-- Volver apantalla inicial -->
                        <br><br>
                        <input type="button" value="Volver al formulario" onclick="location.href='../index.html'">
                        <br><br>
                    </div>
                <?
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
    if (form_mail("mail@dominio.es", 
                "Solicitud de empleo desde la web.", 
                "Los datos introducidos en el formulario son:\n", 
                "mail@dominio.es")) 
        echo "<br><br><p align='center'>Su formulario ha sido enviado con éxito. Gracias por su interés.</p>";
        

?>
        <div align="center">
            <!-- Volver apantalla inicial -->
            <br><br>
            <input type="button" value="Volver al formulario" onclick="location.href='../index.html'">
            <br><br>
        </div>