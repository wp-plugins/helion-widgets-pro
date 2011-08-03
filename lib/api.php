<?php

function helion_get_book_title($bookstore, $ident) {
	
	if($bookstore = h_validate_bookstore($bookstore)) {
		global $wpdb;
		
		$title = $wpdb->get_var($wpdb->prepare("SELECT tytul FROM " . $wpdb->prefix . "helion_books_" . $bookstore . " WHERE ident = %s", h_validate_ident($ident)));
		
		return $title;
	} else {
		return false;
	}
}

function helion_detect_connection_method() {
	if(is_curl_enabled()) {
		return "curl";
	} else if(is_allow_url_fopen_enabled()) {
		return "fopen";
	} else {
		return "none";
	}
}

function helion_get_link($bookstore, $ident, $cyfra = null, $koszyk = null) {
	if($koszyk) {
		$link = "http://" . h_validate_bookstore($bookstore) . ".pl/add/" . get_option("helion_partner_id");
		if($cyfra = h_validate_cyfra($cyfra)) {
			$link .= "/" . $cyfra;
		}
		$link .= "/" . h_validate_ident($ident) . ".htm";
	} else {
		$link = "http://" . h_validate_bookstore($bookstore) . ".pl/view/" . get_option("helion_partner_id");
		if($cyfra = h_validate_cyfra($cyfra)) {
			$link .= "/" . $cyfra;
		}
		$link .= "/" . h_validate_ident($ident) . ".htm";
	}
	
	return $link;
}

function helion_get_book_info($bookstore, $ident) {
	global $wpdb;
	
	$bookstore = h_validate_bookstore($bookstore);
	$ident = h_validate_ident($ident);
	
	if(!$ident || !$bookstore) return false;
	
	if($book = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "helion_books_" . $bookstore . " WHERE ident = '$ident'", ARRAY_A)) {
		return $book;
	} else {
		$method = helion_detect_connection_method();
		
		switch($method) {
			case 'curl':
				$cu = curl_init();
				$curl_get = array("ident" => $ident);
				$curl_url = "http://" . $bookstore . ".pl/plugins/new/xml/ksiazka.cgi";
				curl_setopt($cu, CURLOPT_URL, $curl_url); 
				curl_setopt($cu, CURLOPT_RETURNTRANSFER, 1); 
				curl_setopt($cu, CURLOPT_GET, 1); 
				curl_setopt($cu, CURLOPT_GETFIELDS, $curl_get); 
				$description = @simplexml_load_string(curl_exec($cu));
				curl_close($cu);
				break;
			default: 
				$description = @simplexml_load_file("http://" . $bookstore . 
								".pl/plugins/new/xml/ksiazka.cgi?ident=" . $ident);
				break;
		}
		
		if(!$description)
			return false;
		
		$book['ident'] = $ident;
		$book['isbn'] = $description->isbn;
		$book['tytul'] = $description->tytul;
		$book['autor'] = $description->autor;
		$book['cenadetaliczna'] = $description->cenadetaliczna;
		$book['cena'] = $description->cena;
		$book['opis'] = $description->opis;
		
		return $book;
	}
}

function helion_get_botd($bookstore) {

	$bookstore = h_validate_bookstore($bookstore);
	
	$method = helion_detect_connection_method();
		
	switch($method) {
		case 'curl':
			$cu = curl_init();
			$curl_url = "http://" . $bookstore . ".pl/plugins/xml/lista.cgi?pd=1";
			curl_setopt($cu, CURLOPT_URL, $curl_url); 
			curl_setopt($cu, CURLOPT_RETURNTRANSFER, 1); 
			$description = simplexml_load_string(curl_exec($cu));
			curl_close($cu);
			break;
		default: 
			$description = simplexml_load_file("http://" . $bookstore . 				
				".pl/plugins/xml/lista.cgi?pd=1");
			break;
	}
	
	if($description) {
	
		$book['ident'] = strtolower($description->item->attributes()->ident);
		$book['isbn'] = $description->item->attributes()->isbn;
		$book['ean'] = $description->item->attributes()->ean;
		$book['tytul'] = $description->item->attributes()->tytul;
		$book['autor'] = $description->item->attributes()->autor;
		$book['cenadetaliczna'] = $description->item->attributes()->cenadetaliczna;
		$book['cena'] = $description->item->attributes()->cena;
		$book['znizka'] = $description->item->attributes()->znizka;
		$book['status'] = $description->item->attributes()->status;
		$book['kat'] = $description->item->attributes()->kat;
		$book['marka'] = $description->item->attributes()->marka;
		$book['ts'] = $description->item->attributes()->ts;
		
		return $book;
	} else {
		return false;
	}
}

/**
 * Sprawdza, czy allow_url_fopen jest wł±czone w konfiguracji serwera.
 */
function is_allow_url_fopen_enabled() {
    return (ini_get('allow_url_fopen') == 1) ? true : false;
}

/**
 * Sprawdza, czy cURL jest wł±czone w konfiguracji serwera.
 */
function is_curl_enabled() {
	return (in_array('curl', get_loaded_extensions())) ? true : false;
}

function helion_download_file($src, $dest) {
	switch(helion_detect_connection_method()) {
		case 'fopen':
			@copy($src, $dest);
			return true;
			break;
		case 'curl':
			$out = fopen($dest, 'wb');

			if($out) {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_FILE, $out);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_URL, $src);
				curl_exec($ch);
				curl_close($ch); 
				fclose($out);
			}
			return true;
			break;
		default:
			return false;
			break;
	}
}

function helion_marka($cyfra) {
	switch($cyfra) {
		case '1':
			return 'helion';
			break;
		case '2':
			return 'onepress';
			break;
		case '3':
			return 'editio';
			break;
		case '4':
			return 'sensus';
			break;
		case '5':
			return 'septem';
			break;
		case 'helion':
			return '1';
			break;
		case 'onepress':
			return '2';
			break;
		case 'editio':
			return '3';
			break;
		case 'sensus':
			return '4';
			break;
		case 'septem':
			return '5';
			break;
		default:
			return $cyfra;
			break;
	}
}

function helion_bookstore_available($bookstore) {

	$bookstore = h_validate_bookstore($bookstore);
	
	$bookstores = get_option("helion_bookstores");
	if($bookstores[$bookstore] == "1") {
		return true;
	} else {
		return false;
	}
}

function helion_random_bookstore() {
	$bookstores = get_option("helion_bookstores");
	$selected = array_keys($bookstores, "1");
	if(!empty($selected)) {
		return array_rand($selected, 1);
	} else {
		return false;
	}
}

function helion_parse_search($fraza) {
	global $wpdb;
	// dopuszczalny tylko jeden typ zapytania
	// ident, tytul, autor
	
	$zapytanie = explode(":", $fraza);
	
	switch($zapytanie[0]) {
		case 'ident':
			return "(SELECT * FROM " . $wpdb->prefix . "helion_books_helion WHERE ident LIKE '%" . $zapytanie[1] . "%' AND cena) UNION (SELECT * FROM " . $wpdb->prefix . "helion_books_onepress WHERE ident LIKE '%" . $zapytanie[1] . "%' AND cena) UNION (SELECT * FROM " . $wpdb->prefix . "helion_books_sensus WHERE ident LIKE '%" . $zapytanie[1] . "%' AND cena) UNION (SELECT * FROM " . $wpdb->prefix . "helion_books_septem WHERE ident LIKE '%" . $zapytanie[1] . "%' AND cena)";
			break;
		case 'autor':
			return "(SELECT * FROM " . $wpdb->prefix . "helion_books_helion WHERE autor LIKE '%" . $zapytanie[1] . "%' AND cena) UNION (SELECT * FROM " . $wpdb->prefix . "helion_books_onepress WHERE autor LIKE '%" . $zapytanie[1] . "%' AND cena) UNION (SELECT * FROM " . $wpdb->prefix . "helion_books_sensus WHERE autor LIKE '%" . $zapytanie[1] . "%' AND cena) UNION (SELECT * FROM " . $wpdb->prefix . "helion_books_septem WHERE autor LIKE '%" . $zapytanie[1] . "%' AND cena)";
			break;
		case 'tytul':
			return "(SELECT * FROM " . $wpdb->prefix . "helion_books_helion WHERE tytul LIKE '%" . $zapytanie[1] . "%' AND cena) UNION (SELECT * FROM " . $wpdb->prefix . "helion_books_onepress WHERE tytul LIKE '%" . $zapytanie[1] . "%' AND cena) UNION (SELECT * FROM " . $wpdb->prefix . "helion_books_sensus WHERE tytul LIKE '%" . $zapytanie[1] . "%' AND cena) UNION (SELECT * FROM " . $wpdb->prefix . "helion_books_septem WHERE tytul LIKE '%" . $zapytanie[1] . "%' AND cena)";
	}
}

function helion_wyszukiwarka($fraza) {
	global $wpdb;
	
	return $wpdb->get_results("(SELECT * FROM " . $wpdb->prefix . "helion_books_helion WHERE ident LIKE '%" . $fraza . "%' OR isbn LIKE '%" . $fraza . "%' OR tytul LIKE '%" . $fraza . "%' OR tytul_orig LIKE '%" . $fraza . "%' OR autor LIKE '%" . $fraza . "%') UNION DISTINCT (SELECT * FROM " . $wpdb->prefix . "helion_books_onepress WHERE ident LIKE '%" . $fraza . "%' OR isbn LIKE '%" . $fraza . "%' OR tytul LIKE '%" . $fraza . "%' OR tytul_orig LIKE '%" . $fraza . "%' OR autor LIKE '%" . $fraza . "%') UNION DISTINCT (SELECT * FROM " . $wpdb->prefix . "helion_books_sensus WHERE ident LIKE '%" . $fraza . "%' OR isbn LIKE '%" . $fraza . "%' OR tytul LIKE '%" . $fraza . "%' OR tytul_orig LIKE '%" . $fraza . "%' OR autor LIKE '%" . $fraza . "%') UNION DISTINCT (SELECT * FROM " . $wpdb->prefix . "helion_books_septem WHERE ident LIKE '%" . $fraza . "%' OR isbn LIKE '%" . $fraza . "%' OR tytul LIKE '%" . $fraza . "%' OR tytul_orig LIKE '%" . $fraza . "%' OR autor LIKE '%" . $fraza . "%')", ARRAY_A);
}

function helion_losowe_ksiazki($bookstore = '', $ilosc = 5) {
	global $wpdb;
	
	$bookstore = h_validate_bookstore($bookstore);
	
	if($bookstore) {
		$sql = "SELECT * FROM " . $wpdb->prefix . "helion_books_" . $bookstore . " ORDER BY RAND() LIMIT " . $ilosc;
	} else {
		$selected = get_option("helion_bookstores");
		
		foreach($selected as $s => $v) {
			if($v)
				$bookstores[] = $wpdb->prefix . "helion_books_" . $s;
		}
		
		$sql = "SELECT * FROM " . $bookstores[array_rand($bookstores)] . " ORDER BY RAND() LIMIT " . $ilosc;
	}
	
	$result = $wpdb->get_results($sql, ARRAY_A);
	
	return $result;
}

function helion_zamien_znacznik($marker, $substitute, $template) {

	$template = preg_replace("/%" . $marker . "%/", $substitute, $template);
	
	return $template;
}

function helion_parse_template($template, $dane) {

	$template = preg_replace("/%ident%/", $dane['ident'], $template);
	$template = preg_replace("/%isbn%/", $dane['isbn'], $template);
	$template = preg_replace("/%tytul%/", $dane['tytul'], $template);
	$template = preg_replace("/%tytul_orig%/", $dane['tytul_orig'], $template);
	$template = preg_replace("/%autor%/", $dane['autor'], $template);
	$template = preg_replace("/%tlumacz%/", $dane['tlumacz'], $template);
	$template = preg_replace("/%cena%/", $dane['cena'], $template);
	$template = preg_replace("/%cenadetaliczna%/", $dane['cenadetaliczna'], $template);
	$template = preg_replace("/%znizka%/", $dane['znizka'], $template);
	$template = preg_replace("/%marka%/", ucfirst(helion_marka($dane['marka'])), $template);
	$template = preg_replace("/%nazadanie%/", $dane['nazadanie'], $template);
	$template = preg_replace("/%format%/", $dane['format'], $template);
	$template = preg_replace("/%liczbastron%/", $dane['liczbastron'], $template);
	$template = preg_replace("/%oprawa%/", $dane['oprawa'], $template);
	$template = preg_replace("/%nosnik%/", $dane['nosnik'], $template);
	$template = preg_replace("/%datawydania%/", $dane['datawydania'], $template);
	$template = preg_replace("/%issueurl%/", $dane['issueurl'], $template);
	$template = preg_replace("/%online%/", $dane['online'], $template);
	$template = preg_replace("/%bestseller%/", $dane['bestseller'], $template);
	$template = preg_replace("/%nowosc%/", $dane['nowosc'], $template);
	$template = preg_replace("/%opis%/", $dane['opis'], $template);
	
	
	$okladki = array("65x85", "72x95", "88x115", "90x119", "120x156", "125x163", "181x236", "326x466");
	
	foreach($okladki as $o) {
		if(preg_match("/%okladka" . $o . "%/", $template)) {
			$okladka = helion_get_cover(helion_marka($dane['marka']), $dane['ident'], $o);
			$template = preg_replace("/%okladka" . $o . "%/", '<img src="' . $okladka['src'] . '" alt="' . $okladka['alt'] . '" />', $template);
		}
	}
	

	$link = helion_get_link(helion_marka($dane['marka']), $dane['ident'], null, false);
	$dokoszyka = helion_get_link(helion_marka($dane['marka']), $dane['ident'], null, true);
	
	$template = preg_replace("/%link%/", $link, $template);
	$template = preg_replace("/%dokoszyka%/", $dokoszyka, $template);
	
	return $template;
}

function helion_parse_bookstore_template($template) {

	global $wpdb;
	
	if($ksiegarnia = h_validate_bookstore(get_option("helion_bookstore_ksiegarnia"))) {
	
		if(preg_match("/%nowosci%/", $template)) {
			$nowosci = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "helion_books_" . $ksiegarnia . " WHERE nowosc AND marka = " . helion_marka($ksiegarnia) . " ORDER BY RAND() LIMIT 4", ARRAY_A);
			
			foreach($nowosci as $nowosc) {
				$okladka = helion_get_cover($ksiegarnia, $nowosc['ident'], "72x95");
				$dokoszyka = helion_get_link($ksiegarnia, $nowosc['ident'], null, true);
				$ksiazka = get_bloginfo("home") . '/' . get_option("helion_bookstore_slug") . '/?helion_bookstore=book&ksiegarnia=' . $ksiegarnia . '&ident=' . $nowosc['ident'];
				
				$pozycja = '<div class="helion-nowosc">';
				$pozycja .= '<a href="' . $ksiazka . '"><img src="' . $okladka['src'] . '" /></a>';
				$pozycja .= '<div class="info">';
				if(strlen($nowosc['tytul']) <= 46) {
					$pozycja .= '<p><a href="' . $ksiazka . '" title="' . $nowosc['tytul'] . '">' . $nowosc['tytul'] . '</a></p>';
				} else {
					$pozycja .= '<p><a href="' . $ksiazka . '" title="' . $nowosc['tytul'] . '">' . helion_snippet(strip_tags($nowosc['tytul']), 46, "...") . '</a></p>';
				}
				
				if(strlen($nowosc['autor']) <= 46) {
					$pozycja .= '<span>Autor: ' . $nowosc['autor'] . '</span>';
				} else {
					$pozycja .= '<span>Autor: ' . substr($nowosc['autor'], 0, 46) . '...</span>';
				}
				
				$pozycja .= '</div>';
				$pozycja .= '<div class="clear"></div>';
				$pozycja .= '<div class="helion-box">';
				
				if($nowosc['znizka']) {
					$pozycja .= '<div class="helion-cena">' . $nowosc['cena'] . ' zł (-' . $nowosc['znizka'] . ')</div>';
				} else {
					$pozycja .= '<div class="helion-cena">' . $nowosc['cena'] . ' zł</div>';
				}
				
				$pozycja .= '<a href="' . $dokoszyka . '">kup teraz</a>';
				$pozycja .= '</div>';
				$pozycja .= '</div>';
				
				$n[] = $pozycja;
			}

			$template = preg_replace("/%nowosci%/", implode("\n", $n) . '<div class="clear"></div>', $template);
		}
		
		if(preg_match("/%bestsellery%/", $template)) {
			$bs_table = $wpdb->prefix . "helion_bestsellers";
			$ks_table = $wpdb->prefix . "helion_books_" . $ksiegarnia;
			
			$sql = "SELECT * FROM $bs_table, $ks_table WHERE ($bs_table.ident = $ks_table.ident) AND $bs_table.bookstore = '" . $ksiegarnia . "' ORDER BY RAND() LIMIT 10";
			
			$bestsellery = $wpdb->get_results($sql, ARRAY_A);
			
			//print_r($sql);
			//print_r($bestsellery);
			
			foreach($bestsellery as $bestseller) {
				$okladka = helion_get_cover($ksiegarnia, $bestseller['ident'], "125x163");
				$dokoszyka = helion_get_link($ksiegarnia, $bestseller['ident'], null, true);
				$ksiazka = get_bloginfo("home") . '/' . get_option("helion_bookstore_slug") . '/?helion_bookstore=book&ksiegarnia=' . $ksiegarnia . '&ident=' . $bestseller['ident'];
				
				$pozycja = '<div class="helion-bestseller">';
				$pozycja .= '<a href="' . $ksiazka . '"><img src="' . $okladka['src'] . '" /></a>';
				$pozycja .= '<div class="info">';
				if(strlen($bestseller['tytul']) <= 46) {
					$pozycja .= '<p><a href="' . $ksiazka . '" title="' . $bestseller['tytul'] . '">' . $bestseller['tytul'] . '</a></p>';
				} else {
					$pozycja .= '<p><a href="' . $ksiazka . '" title="' . $bestseller['tytul'] . '">' . helion_snippet(strip_tags($bestseller['tytul']), 46) . '</a></p>';
				}
				
				if(strlen($bestseller['autor']) <= 46) {
					$pozycja .= '<span>Autor: ' . $bestseller['autor'] . '</span>';
				} else {
					$pozycja .= '<span>Autor: ' . substr($bestseller['autor'], 0, 46) . '...</span>';
				}
				
				$pozycja .= '<p>' . helion_snippet(strip_tags($bestseller['opis']), 324, "...") . '</p>';				
				$pozycja .= '</div>';
				$pozycja .= '<div class="clear"></div>';
				$pozycja .= '<div class="helion-box">';
				
				if($bestseller['znizka']) {
					$pozycja .= '<div class="helion-cena">' . $bestseller['cena'] . ' zł (-' . $bestseller['znizka'] . ')</div>';
				} else {
					$pozycja .= '<div class="helion-cena">' . $bestseller['cena'] . ' zł</div>';
				}
				
				$pozycja .= '<a href="' . $dokoszyka . '">kup teraz</a>';
				$pozycja .= '</div>';
				$pozycja .= '</div>';
				
				$b[] = $pozycja;
			}

			if(is_array($b)) {
				$template = preg_replace("/%bestsellery%/", implode("\n", $b), $template);
			} else {
				$template = preg_replace("/%bestsellery%/", $b, $template);
			}
		}
		
		return $template;
		
	} else {
	
		return "<p>Nie wybrano księgarni. Przejdź do menu Helion->Księgarnia i skonfiguruj wtyczkę.</p>";
		
	}
}

function helion_snippet($text, $length, $tail = "...") {
	$text = trim($text);
	$txtl = strlen($text);
	if($txtl > $length) {
		for($i=1;$text[$length-$i]!=" ";$i++) {
			if($i == $length) {
				return substr($text,0,$length) . $tail;
			}
		}
		$text = substr($text,0,$length-$i+1) . $tail;
	}
	return $text;
}

function helion_get_kategorie($bookstore) {

	$bookstore = h_validate_bookstore($bookstore);
	
	if($lista = get_option("helion_kategorie_" . $bookstore)) {
		return $lista;
	} else {
		$method = helion_detect_connection_method();
		
		$url = "http://$bookstore.pl/plugins/new/xml/lista-katalog.cgi";
		
		switch($method) {
			case 'curl':
				$cu = curl_init();
				
				curl_setopt($cu, CURLOPT_URL, $url); 
				curl_setopt($cu, CURLOPT_RETURNTRANSFER, 1); 
				$xml = curl_exec($cu);
				curl_close($cu);
				break;
				
			default: 
				$xml = file_get_contents($url);
				break;
		}
		
		$xml = simplexml_load_string($xml);
		
		$lista = array("nad" => array(), "pod" => array());
		
		foreach($xml as $item) {
			$grupa_nad = (string) $item->attributes()->grupa_nad;
			$id_nad = (string) $item->attributes()->id_nad;
			
			$grupa_pod = (string) $item->attributes()->grupa_pod;
			$id_pod = (string) $item->attributes()->id_pod;
			
			$lista['nad'][$id_nad] = $grupa_nad;
			
			if($id_pod) {
				$lista['pod'][$id_pod] = array($id_nad => $grupa_pod);
			}
		}
		
		update_option("helion_kategorie_" . $bookstore, $lista);
		
		return $lista;
	}
}

function helion_parse_category_template($template, $kategoria, $page = 0) {

	$kategoria = h_validate_catid($kategoria);
	
	if($page) {
		$page = h_validate_page($page);
		if($page == false) return "<p>Nieprawidłowy numer strony.</p>";
	}
	
	if(!$kategoria) return "<p>Nieprawidłowa kategoria.</p>";
	
	global $wpdb;
	
	$slug = get_option("helion_bookstore_slug");
	$home = get_bloginfo('home');
	if($slug) {
		$home_url = $home . "/" . $slug . "/";
	} else {
		$home_url = $home . "/";
	}
	
	if($page) {
		$p = $page * 10;
	} else {
		$p = 0;
	}
	
	if($ksiegarnia = h_validate_bookstore(get_option("helion_bookstore_ksiegarnia"))) {
		
		if(preg_match("/%kategoria%/", $template)) {
	
			$sql = "SELECT * FROM " . $wpdb->prefix . "helion_books_" . $ksiegarnia . " WHERE cena AND serietematyczne LIKE '%id=\"" . $kategoria . "\"%' LIMIT " . $p . ", 10";
			
			$dane = $wpdb->get_results($sql, ARRAY_A);
			
			foreach($dane as $ksiazka) {
				$okladka = helion_get_cover($ksiegarnia, $ksiazka['ident'], "125x163");
				$dokoszyka = helion_get_link($ksiegarnia, $ksiazka['ident'], null, true);
				$url = get_bloginfo("home") . '/' . $slug . '/?helion_bookstore=book&ksiegarnia=' . $ksiegarnia . '&ident=' . $ksiazka['ident'];
				
				$pozycja = '<div class="helion-kategoria">';
				$pozycja .= '<a href="' . $url . '"><img src="' . $okladka['src'] . '" /></a>';
				$pozycja .= '<div class="info">';
				if(strlen($ksiazka['tytul']) <= 46) {
					$pozycja .= '<p><a href="' . $url . '" title="' . $ksiazka['tytul'] . '">' . $ksiazka['tytul'] . '</a></p>';
				} else {
					$pozycja .= '<p><a href="' . $url . '" title="' . $ksiazka['tytul'] . '">' . helion_snippet(strip_tags($ksiazka['tytul']), 46) . '</a></p>';
				}
				
				if(strlen($ksiazka['autor']) <= 46) {
					$pozycja .= '<span>Autor: ' . $ksiazka['autor'] . '</span>';
				} else {
					$pozycja .= '<span>Autor: ' . substr($ksiazka['autor'], 0, 46) . '...</span>';
				}
				
				$pozycja .= '<p>' . helion_snippet(strip_tags($ksiazka['opis']), 324, "...") . '</p>';				
				$pozycja .= '</div>';
				$pozycja .= '<div class="clear"></div>';
				$pozycja .= '<div class="helion-box">';
				
				if($ksiazka['znizka']) {
					$pozycja .= '<div class="helion-cena">' . $ksiazka['cena'] . ' zł (-' . $ksiazka['znizka'] . ')</div>';
				} else {
					$pozycja .= '<div class="helion-cena">' . $ksiazka['cena'] . ' zł</div>';
				}
				
				$pozycja .= '<a href="' . $dokoszyka . '">kup teraz</a>';
				$pozycja .= '</div>';
				$pozycja .= '</div>';
				
				$b[] = $pozycja;
			}
			
			if(is_array($b)) {
				$template = preg_replace("/%kategoria%/", implode("\n", $b), $template);
			} else {
				$template = preg_replace("/%kategoria%/", $b, $template);
			}
			
			$paginacja = '<div class="paginacja">';
			
			if($page > 0) {
				$paginacja .= '<div class="poprzednia"><a href="' . $home_url . '?helion_bookstore=category&id=' . $kategoria . '&helion_page=' . ($page - 1) . '">&laquo; Poprzednia strona</a></div>';
			}
			
			$sql2 = "SELECT COUNT(*) FROM " . $wpdb->prefix . "helion_books_" . $ksiegarnia . " WHERE cena AND serietematyczne LIKE '%id=\"" . $kategoria . "\"%'";
			
			$ilosc_wynikow = $wpdb->get_var($sql2);

			if($ilosc_wynikow > $p + 10) {
				$paginacja .= '<div class="nastepna"><a href="' . $home_url . '?helion_bookstore=category&id=' . $kategoria . '&helion_page=' . ($page + 1) . '">Następna strona &raquo;</a></div>';
			}
			
			$paginacja .= '</div>';
			
			$template = preg_replace("/%paginacja%/", $paginacja, $template);
			
			return $template;
		}
	} else {
		return "<p>Nie wybrano księgarni. Przejdź do menu Helion->Księgarnia i skonfiguruj wtyczkę.</p>";
	}
}

function helion_ksiegarnie_wlaczone() {
	if(is_array($ks = get_option("helion_bookstores"))) {
		foreach($ks as $k => $val) {
			if($val) return true;
		}
		return false;
	} else {
		return false;
	}
}

function get_ID_by_slug($page_slug) {
    $page = get_page_by_path($page_slug);
    if ($page) {
        return $page->ID;
    } else {
        return null;
    }
}

function get_slug_by_ID($page_id) {
	global $wpdb;
	
	$slug = $wpdb->get_var("SELECT post_name FROM " . $wpdb->posts . " WHERE ID = '" . $page_id . "'");
	return $slug;
}

function h_validate_bookstore($bookstore) {
	if($bookstore == 'helion' || $bookstore == 'onepress' || $bookstore == 'sensus' || $bookstore == 'septem') {
		return $bookstore;
	} else {
		return false;
	}
}

function h_validate_ident($ident) {
	if(preg_match("/^[A-Za-z0-9]+$/", $ident)) {
		return $ident;
	} else {
		return false;
	}
}

function h_validate_cyfra($cyfra) {
	if(preg_match("/^[0-9]+$/", $cyfra)) {
		return $cyfra;
	} else {
		return false;
	}
}

function h_validate_size($size) {
	if(preg_match("/^[0-9x]+$/", $size)) {
		return $size;
	} else {
		return false;
	}
}

function h_validate_catid($catid) {
	if(preg_match("/^[0-9]+$/", $catid)) {
		return $catid;
	} else {
		return false;
	}
}

function h_validate_page($page) {
	if(preg_match("/^[0-9]+$/", $page)) {
		return $page;
	} else {
		return false;
	}
}

function helion_clear_random_on_disable($bookstore) {
	$bookstore = h_validate_bookstore($bookstore);
	
	if(!$bookstore) return false;
	
	global $wpdb;
	
	$wpdb->query("DELETE FROM " . $wpdb->prefix . "helion_widget_random WHERE typ = '" . $bookstore . "' OR (typ = 'ksiegarnia' AND obiekt = '" . $bookstore . "')");
}

?>
