<?php

/**
 * Tu są shortcody:
 * [helion_ksiazka]
 * [helion_link]
 * [helion_wyniki_wyszukiwania]
 */

add_shortcode( 'helion_ksiazka', 'helion_ksiazka' );
add_shortcode( 'helion_link', 'helion_link' );
add_shortcode( 'helion_wyniki_wyszukiwania', 'helion_wyniki_wyszukiwania' );

add_action('wp_print_styles', 'helion_shortcode_styles');

function helion_shortcode_styles() {
	wp_register_style('helion-shortcodes', plugins_url("css/shortcodes.css", dirname(__FILE__)));
	wp_enqueue_style('helion-shortcodes');
}

/**
 * Parametry:
 * @param ksiegarnia
 * @param ident
 * @param okladka rozmiar okładki
 * @param width szerokość (bez px)
 * @param float atrybut float (left|right)
 *
 */
function helion_ksiazka($atts) {
	
	extract( shortcode_atts( array(
		'ksiegarnia' => 'helion',
		'ident' => 'markwy',
		'okladka' => '181x236',
		'width' => '',
		'float' => 'left',
	), $atts ) );
	
	switch($float) {
		case 'left':
		case 'lewy':
		case 'lewo':
		case 'l':
			$float = "alignleft";
			break;
		case 'right':
		case 'prawo':
		case 'prawy':
		case 'r':
		case 'p':
			$float = "alignright";
			break;
		default:
			$float = "";
			break;
	}
	
	$wymiary = explode("x", $okladka);
	
	if(!$width)
		 // albo rozmiar okładki, albo 200, które większe
		$width = max($wymiary[0], 200);
		
	
		
	$book = helion_get_book_info($ksiegarnia, $ident);
	if($book) {
		$link = helion_get_link($ksiegarnia, $ident, $cyfra, true);
		$okladka = helion_get_cover($ksiegarnia, $ident, $okladka);
		$tytul = $okladka['alt'];
		$autor = $book['autor'];

		$cont = '<div class="helion-ksiazka ' . "helion-ksiazka-" . $ksiegarnia . "-" . $ident . ' ' . $float . '" style="width: ' . $width . 'px">';
		$cont .= '<div class="helion_okladka" style="width: ' . $wymiary[0] . 'px;">';
		$cont .= '<p><a href="' . $link . '" target="_blank" title="' . $tytul . '">';
		$cont .= '<img src="' . $okladka['src'] . '" alt="' . $okladka['alt'] . '" />';
		$cont .= '</a></p>';
		$cont .= '</div>';
		$cont .= '<div class="helion_meta">';
		$cont .= '<p class="helion_tytul"><a href="' . $link . '" target="_blank">' . $tytul . '</a></p>';
		$cont .= '<p class="helion_autor">autor: ' . $autor . '</p>';
		$cont .= '<p class="helion_isbn">ISBN: ' . $book['isbn'] . '</p>';
		$cont .= '<p class="helion_format">Format: ' . $book['format'] . ', stron: ' . $book['liczbastron'] . '</p>';
		$cont .= '<p class="helion_data">Data wydania: ' . $book['datawydania'] . '</p>';
		$cont .= '<p class="helion_cena">Cena: ' . $book['cenadetaliczna'] . 'zł</p>';
		$cont .= '<div class="helion-box">';
		$cont .= '<a href="' . $link . '">kup teraz</a>';
		$cont .= '</div>';
		$cont .= '</div>';
		$cont .= '</div>';
		
		return $cont;
	} else {
		return "Wystąpił błąd. Nie znaleziono takiej ksiązki.";
	}
}

/**
 * @param ksiegarnia
 * @param ident
 * @param cyfra
 */
function helion_link($atts, $content = null) {
	extract( shortcode_atts( array(
		'ksiegarnia' => 'helion',
		'ident' => 'markwy',
		'cyfra' => '',
	), $atts ) );
	
	$book = helion_get_book_info($ksiegarnia, $ident);
	if($book) {
		$link = helion_get_link($ksiegarnia, $ident, $cyfra);
		
		if($content) {
			$cont = '<a href="' . $link . '" target="_blank" title="' . $book['tytul'] . '">' . $content . '</a>';
		} else {
			$cont = '<a href="' . $link . '" target="_blank" title="' . $book['tytul'] . '">' . $book['tytul'] . '</a>';
		}
		
		return $cont;
	} else {
		return "Wystąpił błąd. Nie znaleziono takiej książki.";
	}
}

function helion_wyniki_wyszukiwania() {

	$slug = get_option("helion_wyszukiwarka_slug");
	
	if($_REQUEST['helion_wyszukiwarka']) {
            
		$template = '<div class="helion_wyszukiwarka_wyniki">';
		
		$template .= stripslashes(get_option("helion_wyszukiwarka_template"));
		
		$wynik = helion_wyszukiwarka($_REQUEST['helion_wyszukiwarka']);
		
		if(!$_REQUEST['helion_serp'] && $wynik) {

			$template = helion_parse_template($template, $wynik[0]);
			
			$template .= '</div>';
			
			return $template;
			
		} else if($_REQUEST['helion_serp'] && $wynik) {
			
			$dup_idents = array();
			
			$lista_wynikow = '<h3>Wyszukano frazę: <em>&ldquo;' . strip_tags($_REQUEST['helion_wyszukiwarka']) . '&rdquo;</em></h3>';
			
			$lista_wynikow .= '<ol class="helion_serp">';
			
			foreach($wynik as $w) {
				if(!in_array($w['ident'], $dup_idents)) {
					$lista_wynikow .= '<li><a href="' . $slug . '/?helion_wyszukiwarka=' . $w['ident'] . '">' . $w['autor'] . " - " . $w['tytul'] . '</a></li>';
					$dup_idents[] = $w['ident'];
				}
			}
			
			$lista_wynikow .= '</ol>';
			
			return $lista_wynikow;
			
		} else {
		
			$dup_idents = array();
			
			$output = "<p><strong>Nie znaleziono żadnej książki pasującej do zapytania <em>" . strip_tags($_REQUEST['helion_wyszukiwarka']) . "</em>.</strong></p>";
			$output .= "<p>Być może zainteresuje cię jedna z poniższych pozycji:</p>";
			
			$losowe = helion_losowe_ksiazki();
			
			$output .= '<ol class="helion_serp">';
			
			foreach($losowe as $l) {
				if(!in_array($l['ident'], $dup_idents)) {
					$output .= '<li><a href="' . $slug . '/?helion_wyszukiwarka=' . $l['ident'] . '">' . $l['autor'] . " - " . $l['tytul'] . '</a></li>';
					$dup_idents[] = $l['ident'];
				}
			}
			
			$output .= '</ol>';
			$output .= '<p>Możesz także zmienić treść zapytania i poszukać książek w oparciu o inne słowa kluczowe.</p>';
			
			return $output;
		}
	} else {
	
		$dup_idents = array();
		
		$output = "<p><strong>Nie znaleziono żadnej książki pasującej do zapytania <em>" . strip_tags($_REQUEST['helion_wyszukiwarka']) . "</em>.</strong></p>";
		$output .= "<p>Być może zainteresuje cię jedna z poniższych pozycji:</p>";
		
		$losowe = helion_losowe_ksiazki();
		
		$output .= '<ol class="helion_serp">';
		
		foreach($losowe as $l) {
			if(!in_array($l['ident'], $dup_idents)) {
				$output .= '<li><a href="' . $slug . '/?helion_wyszukiwarka=' . $l['ident'] . '">' . $l['autor'] . " - " . $l['tytul'] . '</a></li>';
				$dup_idents[] = $l['ident'];
			}
		}
		
		$output .= '</ol>';
		$output .= '<p>Możesz także zmienić treść zapytania i poszukać książek w oparciu o inne słowa kluczowe.</p>';
		
		return $output;
	}
}

?>