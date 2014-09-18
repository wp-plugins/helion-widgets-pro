=== Helion Widgets Pro ===
Contributors: mdzimiera, Grupa Wydawnicza Helion
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=63SBY4W2R42NW
Tags: helion, sensus, onepress, septem, ebookpoint, bezdroza, program partnerski, księgarnia
Requires at least: 3.1
Tested up to: 4.0
Stable tag: 1.2.8

Zainstaluj na swoim blogu widgety z książkami, umieszczaj informacje o książkach we wpisach, otwórz własną księgarnię i zarabiaj z GW Helion!

== Description ==

Wzbogać swój serwis o ciekawe treści, które przyciągną do Ciebie klientów! Rozwiń skrzydła w e-biznesie i zacznij dobrze zarabiać. Poszerz swoją ofertę o nowości oraz bestsellery literatury informatycznej, biznesowej, przewodniki turystyczne, beletrystykę oraz poradniki psychologiczne. Pamiętaj książki informatyczne to najlepiej sprzedające się pozycje w sieci!

Program partnerski działa praktycznie bezobsługowo a jego zasady są proste i przejrzyste. Partner może publikować wszystkie informacje o książkach dostępnych w księgarniach Grupy Wydawniczej Helion, a mianowicie: onepress.pl, helion.pl, sensus.pl, septem.pl i ebookpoint.pl, w tym fragmentów książek, okładek, filmów video, szczegółowych opisów oraz spisów treści wraz z mechanizmem dodawania książek
do koszyka.

W zamian za prezentację naszych produktów otrzymasz wynagrodzenie w postaci prowizji od każdego zrealizowanego zakupu w księgarniach za pośrednictwem swojej strony. Prowizja od każdego zrealizowanego zamówienia wynosi 5% dla książek drukowanych oraz 15% w przypadku publikacji elektronicznych.

Już teraz zapoznaj się z Programem Partnerskim na stronie http://program-partnerski.helion.pl i dołącz do 4 tysięcy partnerów współpracujących z nami!

Wtyczka pozwala na:

*  umieszczanie na blogu widgetów z książkami (wybrane książki, książka dnia, bestsellery, wyszukiwarka)
*  łatwe umieszczanie informacji o książkach i linków we wpisach
*  stworzenie na blogu własnej księgarni zawierającej wszystkie pozycje z dowolnej księgarni GW Helion

== Installation ==

Aby zainstalować wtyczkę, najlepiej wykonać to z poziomu panelu administracyjnego:

1. Przejdź do menu Wtyczki->Dodaj nową
1. Wyszukaj wtyczkę Helion Widgets Pro
1. Kliknij zainstaluj i potwierdź
1. Po instalacji aktywuj wtyczkę i przejdź do menu Helion, aby ją skonfigurować

Możesz także zainstalować wtyczkę pobierając ją samodzielnie i wgrywając na serwer:

1. Pobierz pakiet .zip z wtyczką
1. W panelu administracyjnym bloga przejdź do Wtyczki->Dodaj nową
1. Wybierz opcję "Wgraj na serwer"
1. Po wgraniu wtyczki aktywuj ją i przejdź do nowego menu Helion, aby podać swoje dane i wybrać księgarnie

Trzecią opcją jest wgranie folderu z wtyczką przez FTP do katalogu `/wp-content/plugins/`.

== Frequently Asked Questions ==

= Czy muszę być uczestnikiem Programu Partnerskiego Helion? =

Aby używać tej wtyczki, musisz być uczestnikiem Programu Partnerskiego. Przejdź pod adres http://program-partnerski.helion.pl/

= Jakie wymagania ma wtyczka? =

Wtyczka tworzy aktualną kopię bazy danych o książkach każdej księgarni, którą wybierzesz. Dlatego przede wszystkim upewnij się, że twoja baza danych posiada około 6MB miejsca dla danych na temat księgarni Helion, a po około 3MB dla każdej z pozostałych księgarni.

= Gdzie znajdę informacje o konfiguracji wtyczki? =

Wszystko jest opisane w menu Helion->Pomoc, w panelu administracyjnym twojego bloga.

= Czy mogę używać wtyczkę na WordPress MultiSite? =

W tej chwili wtyczka nie obsługuje jeszcze WordPress MultiSite (sieci blogów), ale planujemy dodać taką możliwość w przyszłości.

= Czy wtyczka może działać na hostingu z ograniczeniami nałożonymi na PHP? =

Wtyczka wykorzystuje funckje, które nie są dostępne na hostingach z włączonym safe mode, a także na hostingach z wyłączonymi niektórymi funkcjami (zwłaszcza darmowych), w tym shell_exec(). Może wtedy pojawić się informacja o błędzie:

`shell_exec() has been disabled for security reasons`

W takiej sytuacji należy poprosić administratora hostingu o włączenie shell_exec() lub zmienić hosting.

= Mam problemy z obsługą wtyczki. Gdzie znajdę pomoc? =

Pomoc znajdziesz, rejestrując się na forum Programu Partnerskiego Helion pod adresem http://program-partnerski.helion.pl/forum/

= Znalazłem błąd. Gdzie mogę go zgłosić? =

Napisz do autora wtyczki na mdzimiera@helion.pl lub zaloguj się na forum  PP Helion pod adresem http://program-partnerski.helion.pl/forum/index.php

== Screenshots ==

1. Moduł Księgarni - nowości
2. Widget Książka Dnia
3. Panel administracyjny - wybór książek do wyświetlania w widgecie Losowa Książka

== Changelog ==

= 0.90 =
* Pierwsza publiczna wersja wtyczki
* Podziękowania dla Jakuba Milczarka za testy wersji beta

= 0.91 =
* Poprawiony błąd w wyszukiwarce - podpowiedzi gdy nie wpisano żadnego zapytania
* Poprawione domyślne style CSS elementów księgarni

= 0.92 =
* Zabezpieczenie w przypadku, gdy próbujemy pobrać dane dla książki w przygotowaniu.

= 0.93 =
* Informacja o błędach jeśli wtyczka działa na hostingu z ograniczeniami nałożonymi na PHP (shell_exec()).

= 0.94 =
* Poprawiony błąd w widgecie Kategorie.

= 0.95 =
* Dodałem poprawnie działającą paginację w kategoriach.
* Poprawiłem kilka drobnych błędów zauważonych przez użytkowników.

= 0.96 =
* Poprawiłem działanie wtyczki na hostingach z wyłączonym shell_exec().

= 0.97 =
* cURL jako domyślna metoda pobierania danych (powinno pewniej działać na niektórych hostingach).

= 0.98 =
* Drobne modyfikacje w mechanizmie cache.
* Poprawione wyświetlanie ceny w widgecie Książka Dnia

= 0.99 =
* Poprawione błędy związane z marką Bezdroża.

= 0.99.1 =
* Usunięte zbędne znaki z szablonów.
* Podziękowania dla Wiktora za intensywne testy i wyszukiwanie błędów.
* Dodatkowe zabezpieczenia.

= 1.0.0 =
* Dodana informacja o znaczeniu nieobowiązkowego parametru cyfra.
* Dodany link do forum, na którym można otrzymać pomoc w sprawie programu i działania wtyczki

= 1.0.1 =
* Dodana obsługa nowej księgarni ebookpoint.pl

= 1.1.1 =
* Poprawiona wysokość zniżki dla książki dnia w ebookpoint.pl na 30%

= 1.1.2 =
* Nowe adresy dla plików XML z danymi o książkach (zmiana w programie)
* Poprawiony błąd z wyświetlaniem niektórych okładek
* Poprawione błędy związane z wyświetlaniem komunikatów przez cURL

= 1.1.3 =
* Poprawiony błąd wywołujący komunikat w sprawie wpdb::prepare

= 1.1.4 =
* Poprawione dodatkowe błędy związane z wpdb::prepare

= 1.1.5 =
* Poprawiony jeszcze jeden błąd z wpdb::prepares

= 1.1.6 =
* Zmiana sposobu wyświetlania zniżki (% -> zł)

= 1.1.7 =
* Poprawiono problem z czyszczeniem bazy


= 1.1.8 =
* Zmieniona funkcja odpowiedzialna za czyszczenie bazy


= 1.1.9 =
* Dodatkowe zmiany w funkcji odpowiedzialnej za czyszczenie bazy


= 1.2.0 =
* Dodano markę Bezdroża
* Nie wyświetlaj rabatu, gdy zniżka == 0
* Poprawa przy usuwaniu wszystkich wierszy danej tabeli (marki)

= 1.2.1 =
* Gdy brak ustawienia dot. kategorii, użyj domyślnego
* Dodano brakujący parametr przy czyszczeniu bazy

= 1.2.2 =
* Poprawiono problem z kategoriami w bezdrożach

= 1.2.3 =
* Zmiana pobierania nowości - według daty wydania
* Zmiana pobierania bestsellerów
* Rozwiazanie problemu związanego z wyświetlaniem nowości w ebookpoint
* Rozwiązanie problemu z wyświetlaniem pozycji kategorii eBooki
 
= 1.2.4 =
* Tworzenie podkatalogów w cache w przypadku ich braku
* Poprawka przy wyświetlaniu ebook'ów w księgarni helion
* Poprawione puste linki po wejściu w szczegóły książki

= 1.2.5 =
* Poprawka przy budowaniu linku do koszyka dla pozycji marki editio
* Losowa książka - dodanie marki bezdroża

= 1.2.6 =
* Poprawiono problem z wyszukiwarką (linki w wynikach prowadziły do złej pozycji)

= 1.2.7 =
* Aktualizacja FAQ

= 1.2.8 =
* Zmiany odnośnie curl
* zmiany odnośnie nie zamkniętych tagów
* Zmiany odnośnie widoku nowości

== Upgrade Notice ==

= 0.90 =
Pierwsza publiczna wersja wtyczki

= 0.91 =
Poprawione błędy w wyszukiwarce i domyślne style.

= 0.92 =
Zabezpieczenie w przypadku, gdy próbujemy pobrać dane dla książki w przygotowaniu.

= 0.93 =
Wtyczka próbuje zadziałać poprawnie na hostingach z ograniczonym PHP lub podaje komunikat o błędzie.

= 0.94 =
Poprawiony błąd w widgecie Kategorie. Wyłącz i włącz wtyczkę po aktualizacji!

= 0.95 =
Dodana paginacja w kategoriach, poprawione błędy. Wyłącz i włącz wtyczkę po aktualizacji!

= 0.96 =
Poprawione działanie wtyczki na hostingach z wyłączonym shell_exec().

= 0.97 =
cURL jako domyślna metoda pobierania danych (powinno pewniej działać).

= 0.98 =
Poprawione wyświetlanie ceny w widgecie Książka Dnia. Drobne poprawki błędów.

= 0.99 =
Poprawione błędy związane z marką Bezdroża.

= 0.99.1 =
Usunięto błędy i problemy z zabezpieczeniami.

= 1.0.0 =
Dodana informacja o znaczeniu parametru cyfra.

= 1.0.1 =
Dodana obsługa nowej księgarni ebookpoint.pl

= 1.1.1 =
Poprawiona wysokość zniżki dla książki dnia w ebookpoint.pl na 30%

= 1.1.2 =
Ważne zmiany w umiejscowieniu plików XML na serwerach Helion.

= 1.1.3 =
Poprawiony błąd dotyczący wpdb::prepare.

= 1.1.4 =
Poprawione błędy związane z wpdb::prepare.

= 1.1.5 =
Poprawione dodatkowo odkryte błędy

= 1.1.6 =
Zmiana sposobu wyświetlania zniżki

= 1.1.7 =
Poprawiono problem z czyszczeniem bazy

= 1.1.8 =
Zmieniona funkcja odpowiedzialna za czyszczenie bazy

= 1.1.9 =
Poprawione czyszczenie bazy

= 1.2.0 =
Dodano markę Bezdroża

= 1.2.1 =
Gdy brak ustawienia dot. kategorii, użyj domyślnego

= 1.2.2 =
Poprawiono problem z kategoriami w bezdrożach

= 1.2.3 =
Zmiana pobierania nowości - według daty wydania
Zmiana pobierania bestsellerów
Rozwiazanie problemu związanego z wyświetlaniem nowości w ebookpoint
Rozwiązanie problemu z wyświetlaniem pozycji kategorii eBooki

= 1.2.4 =
Tworzenie podkatalogów w cache w przypadku ich braku
Poprawka przy wyświetlaniu ebook'ów w księgarni helion
Poprawione puste linki po wejściu w szczegóły książki

= 1.2.5 =
Poprawka przy budowaniu linku do koszyka dla pozycji marki editio
Losowa książka - dodanie marki bezdroża

= 1.2.6 =
Poprawiono problem z wyszukiwarką (linki w wynikach prowadziły do złej książki)

= 1.2.7 =
Aktualizacja FAQ

= 1.2.8 = 
Pobieranie pozycji przy wyborze marki
Poprawa działania curl
Poprawa widoku nowości, dodanie brakujących atrybutów