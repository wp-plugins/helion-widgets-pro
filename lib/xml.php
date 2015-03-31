<?php

function helion_xml_download($bookstore, $bestseller = false) {

	if($bestseller) {
		$source = "http://" . $bookstore . ".pl/plugins/new/xml/top.cgi";
		$destination = ABSPATH . "/wp-content/helion-cache/xml/bestsellers_" . $bookstore . ".xml";
	} else {
		//$source = "http://" . $bookstore . ".pl/plugins/xml/lista2.xml";
		$source = "http://" . $bookstore . ".pl/xml/produkty-" . $bookstore . ".xml";
		$destination = ABSPATH . "/wp-content/helion-cache/xml/" . $bookstore . ".xml";
	}
	
	if(is_writable(ABSPATH . "/wp-content/helion-cache/xml")) {
		return helion_download_file($source, $destination);
	} else if(mkdir(ABSPATH . "/wp-content/helion-cache/xml", 0775, true)) {
		return helion_download_file($source, $destination);
	} else {
		return false;
	}
}

function helion_xml_remove($bookstore, $bestseller = false) {
	if($bestseller) {
            if(file_exists(ABSPATH . "/wp-content/helion-cache/xml/bestsellers_" . $bookstore . ".xml")){
		return unlink(ABSPATH . "/wp-content/helion-cache/xml/bestsellers_" . $bookstore . ".xml");
            }else{
                return false;
            }
	} else {
            if(file_exists(ABSPATH . "/wp-content/helion-cache/xml/" . $bookstore . ".xml")){
		return unlink(ABSPATH . "/wp-content/helion-cache/xml/" . $bookstore . ".xml");
            }else{
                return false;
            }
	}
}

function helion_clear_books_database($bookstore) {
	global $wpdb;
	
	$wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->prefix . "helion_books_" . $bookstore . " WHERE 1", array()));
}

function helion_clear_bestsellers() {
	global $wpdb;
	
	//$wpdb->query("TRUNCATE TABLE " . $wpdb->prefix . "helion_bestsellers");
	$wpdb->query("DELETE FROM " . $wpdb->prefix . "helion_bestsellers WHERE 1");
}

function helion_download_bestsellers() {
	return helion_download_xmls(true);
}

function helion_import_bestsellers() {
	return helion_import_xmls(true);
}


/**
 * Funkcja dla cronjob
 *
 */
function helion_download_xmls($bestsellers = false) {
	$bookstores = get_option("helion_bookstores");
	
	foreach($bookstores as $bookstore => $selected) {
		if($selected) {
			helion_xml_download($bookstore, $bestsellers);
		}
	}
}

function helion_xml_import($bookstore, $bestseller = false) {
	global $wpdb;
	
	if($bestseller) {
            $filename = ABSPATH . "/wp-content/helion-cache/xml/bestsellers_" . $bookstore . ".xml";
            if(file_exists($filename)){
		if(is_readable($filename)) {
                    if(filesize($filename) > 0){
                        if(($xml = simplexml_load_file($filename)) !== false){    
                            foreach($xml as $ksiazka) {
                                $k['ident'] = strtolower($ksiazka->attributes()->ID);
                                $k['bookstore'] = $bookstore;
                                $wpdb->insert($wpdb->prefix . "helion_bestsellers", $k);
                            }
                        }else{
                            // wrong xml structure
                            return false;
                        }
                    }else{
                        // empty file
                        return false;
                    }
                    helion_xml_remove($bookstore, true);
		} else {
                    return false;
		}
            }else{
                return false;
            }
	} else {
            $filename = ABSPATH . "/wp-content/helion-cache/xml/" . $bookstore . ".xml";
            if(file_exists($filename)){
		if(is_readable($filename)) {
                    helion_clear_books_database($bookstore);
                    if(filesize($filename) > 0){
                        if(($xml = simplexml_load_file($filename)) !== false){
                            foreach($xml->lista->ksiazka as $ksiazka) {
                                    $k['ident'] = strtolower($ksiazka->ident);
                                    $k['isbn'] = $ksiazka->isbn;
                                    foreach($ksiazka->tytul as $tytul) {
                                            if($tytul->attributes()->language == "polski") {
                                                    $k['tytul'] = $tytul;
                                            } else {
                                                    $k['tytul_orig'] = $tytul;
                                            }
                                    }
                                    $k['link'] = $ksiazka->link;
                                    $k['autor'] = $ksiazka->autor;
                                    $k['tlumacz'] = $ksiazka->tlumacz;
                                    $k['cena'] = $ksiazka->cena;
                                    $k['cenadetaliczna'] = $ksiazka->cenadetaliczna;
                                    $k['znizka'] = $ksiazka->znizka;
                                    $k['marka'] = $ksiazka->marka;
                                    $k['nazadanie'] = $ksiazka->nazadanie;
                                    $k['format'] = $ksiazka->format;
                                    $k['liczbastron'] = $ksiazka->liczbastron;
                                    $k['oprawa'] = $ksiazka->oprawa;
                                    $k['nosnik'] = $ksiazka->nosnik;
                                    $k['datawydania'] = $ksiazka->datawydania;
                                    $k['issueurl'] = $ksiazka->issueurl;
                                    $k['online'] = $ksiazka->online;
                                    $k['bestseller'] = $ksiazka->bestseller;
                                    $k['nowosc'] = $ksiazka->nowosc;
                                    $k['videos'] = $ksiazka->videos;
                                    $k['powiazane'] = $ksiazka->powiazane;
                                    $k['kategorie'] = $ksiazka->kategorie->asXML();
                                    $k['seriewydawnicze'] = $ksiazka->seriewydawnicze->asXML();
                                    $k['serietematyczne'] = $ksiazka->serietematyczne->asXML();
                                    $k['opis'] = $ksiazka->opis;

                                    $wpdb->insert($wpdb->prefix . "helion_books_" . $bookstore, $k);
                            }
                        }else{
                            return false;
                        }
                    }else{
                        return false;
                    }
                    helion_xml_remove($bookstore);
		} else {
                    return false;
		}
            }else{
                return false;
            }
	}
}

/**
 * Funkcja dla cronjob
 *
 */
function helion_import_xmls($bestsellers = false) {
	$bookstores = get_option("helion_bookstores");
	
	if($bestsellers) {
	
		helion_clear_bestsellers();
		
		foreach($bookstores as $bookstore => $selected) {
			if($selected) {
				helion_xml_import($bookstore, true);
			}
		}
	} else {
		foreach($bookstores as $bookstore => $selected) {
			helion_clear_books_database($bookstore);
			if($selected) {
				helion_xml_import($bookstore);
			}
		}
	}
}

?>
