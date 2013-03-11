<?php
/**
 * This file is part of MyBB Antispam plugin for MyBB.
 * Copyright (C) 2010-2013 Lukasz Tkacz <lukasamd@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */ 

$l['mybbAntispamName'] = 'Antyspam Akismet';
$l['mybbAntispamDesc'] = 'Ten plugin sprawdza dane użytkownika w systemie Akismet podczas rejestracji.';
$l['mybbAntispamGroupDesc'] = 'Ustawienia dotyczące modyfikacji "Antyspam Akismet"';

$l['mybbAntispamAkismetStatus'] = 'Akismet podczas rejestracji';
$l['mybbAntispamAkismetStatusDesc'] = 'Włącza/wyłącza filtr Akismet podczas rejestracji.';
$l['mybbAntispamAkismetKey'] = 'Klucz Akismet';
$l['mybbAntispamAkismetKeyDesc'] = 'Klucz do systemu Akismet, <b>wymagany do jego użycia</b>.<br />Swój klucz możesz uzyskać na <a href="http://akismet.com/">http://akismet.com/</a>.';

$l['mybbAntispamSblamStatus'] = 'Sblam podczas rejestracji';
$l['mybbAntispamSblamStatusDesc'] = 'Włącza/wyłącza filtr Sblam podczas rejestracji.';
$l['mybbAntispamSblamStatus'] = 'Sblam dla postów niezalogowanych';
$l['mybbAntispamSblamStatusDesc'] = 'Włącza/wyłącza filtr Sblam gdy osoby niezalogowane piszą posty.';

$l['mybbAntispamSblamMode'] = 'Rozszerzona ochrona Sblam - Rejestracja';
$l['mybbAntispamSblamModeDesc'] = 'Jeżeli włączono, Sblam będzie używał mocniejszego filtrowania podczas sprawdzania rejestracji. Ta opcja nie jest zalecana.';
$l['mybbAntispamSblamMode'] = 'Rozszerzona ochrona Sblam - Posty niezalogowanych';
$l['mybbAntispamSblamModeDesc'] = 'Jeżeli włączono, Sblam będzie używał mocniejszego filtrowania podczas pisania postu przez osoby niezalogowane. Ta opcja nie jest zalecana.';

$l['mybbAntispamSblamKey'] = 'Klucz Sblam';
$l['mybbAntispamSblamKeyDesc'] = 'Klucz do systemu Sblam, <b>opcjonalny do jego użycia</b>.<br />Swój klucz możesz uzyskać na <a href="http://sblam.com">http://sblam.com</a>.';

$l['mybbAntispamCaptcha'] = 'Ulepszona captcha';
$l['mybbAntispamCaptchaDesc'] = 'Jeżeli włączono, MyBB będzie używać ulepszonego mechanizmu captcha (wygląda jak standardowa captcha).<br /><b>Musisz wyłączyć wbudowany mechanizm captcha przed użyciem tej opcji.</b>';

$l['mybbAntispamLogTime'] = 'Czas ważności logów';
$l['mybbAntispamLogTimeDesc'] = 'Czas ważności logów w dniach, po tym czasie logi będą usuwane. Wpisz 0 aby wyłaczyć logi.';

$l['mybbAntispamACPLink'] = 'Logi Akismet';
$l['mybbAntispamACPLinkDesc'] = 'Logi odrzuconych rejestracji wykrytych jako spam przez system Akismet.';
$l['mybbAntispamACP_Time'] = 'Czas rejestracji';
$l['mybbAntispamACP_Type'] = 'Typ';
$l['mybbAntispamACP_Username'] = 'Nick';
$l['mybbAntispamACP_IP'] = 'Adres IP';
$l['mybbAntispamACP_UA'] = 'Przeglądarka';
$l['mybbAntispamACP_NoLogs'] = 'Brak logów do wyświetlenia';
