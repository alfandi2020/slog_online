<?php

/**
 * Rupiah Format
 * @param  int $number money in integer format
 * @return string         money in string format
 */
function formatNo($number)
{
    if ($number == 0) {return '';}

    return number_format($number, 0, ',', '.');
}

function formatRp($number)
{
    if ($number == 0) {return '-';}
    return 'Rp. '.formatNo($number);
}

function formatDecimal($number)
{
    return number_format($number, 2, ',', '.');
}

function formatDiscountRp($number)
{
    if ($number == 0) {return '-';}
    return '(Rp. '.formatNo($number).')';
}

function formatDate($date)
{
    if (!$date || $date == '0000-00-00') {
        return null;
    }

    $explodedDate = explode('-', $date);

    if (count($explodedDate) == 3 && checkdate($explodedDate[1], $explodedDate[0], $explodedDate[2])) {
        return $explodedDate[2].'-'.$explodedDate[1].'-'.$explodedDate[0];
    } else if (count($explodedDate) == 3 && checkdate($explodedDate[1], $explodedDate[2], $explodedDate[0])) {
        return $explodedDate[2].'-'.$explodedDate[1].'-'.$explodedDate[0];
    }

    throw new \Exception('Invalid data format.');
}

function dateId($date)
{
    if (is_null($date) || $date == '0000-00-00') {
        return '-';
    }

    $explodedDate = explode('-', $date);

    if (count($explodedDate) == 3 && checkdate($explodedDate[1], $explodedDate[2], $explodedDate[0])) {
        $months = getMonths();
        return $explodedDate[2].' '.$months[$explodedDate[1]].' '.$explodedDate[0];
    }

    throw new \Exception('Invalid data format.');
}

function monthNumber($number)
{
    return str_pad($number, 2, "0", STR_PAD_LEFT);
}

function monthId($monthNumber)
{
    if (is_null($monthNumber)) {
        return $monthNumber;
    }

    $months = getMonths();
    $monthNumber = monthNumber($monthNumber);
    return $months[$monthNumber];
}

function getMonths()
{
    return [
        '01' => 'Januari',
        '02' => 'Pebruari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'Nopember',
        '12' => 'Desember',
    ];
}

function getYears()
{
    $yearRange = range(2017, date('Y'));
    foreach ($yearRange as $year) {
        $years[$year] = $year;
    }
    return $years;
}

function getDays()
{
    return $days = [1 => 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
}

function getDay($dayIndex = null)
{
    $days = getDays();
    if (!is_null($dayIndex) && in_array($dayIndex, range(1, 7))) {
        return $days[$dayIndex];
    }

    return '-';
}

function isValidDate($date)
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

function sanitizeNumber($number)
{
    return str_replace(',', '.', $number);
}

function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2).' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2).' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2).' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes.' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes.' byte';
    } else {
        $bytes = '0 bytes';
    }

    return $bytes;
}

/**
 * Overide Laravel Collective  link_to_route helper function
 * @param  string $name       Name of route
 * @param  string $title      Text that displayed on view
 * @param  array  $parameters URL Parameter
 * @param  array  $attributes The anchor tag atributes
 */
function html_link_to_route($name, $title = null, $parameters = [], $attributes = [])
{
    if (array_key_exists('icon', $attributes)) {
        $title = '<i class="fa fa-'.$attributes['icon'].'"></i> '.$title;
    }

    return app('html')->decode(link_to_route($name, $title, $parameters, $attributes));
}

function displayWeight($weight)
{
    return (is_null($weight)) ? '-' : $weight.' Kg';
}

function getNotificationViewPart($notifClass)
{
    $notifClass = str_replace('App\Notifications\\', '', $notifClass);
    $notifClass = str_replace('\\', '.', $notifClass);
    $notifClass = str_slug(snake_case($notifClass));
    return $notifClass;
}

function generateBarcode($number)
{
    return Html::image(url('barcode/img/'.$number.'/25/2'), $number);
}

function monthDateArray($year, $month)
{
    $dateCount = Carbon::parse($year.'-'.$month)->format('t');
    $dates = [];
    foreach (range(1, $dateCount) as $dateNumber) {
        $dates[] = str_pad($dateNumber, 2, '0', STR_PAD_LEFT);
    }

    return $dates;
}

function cleanUpCustomerInvoiceNo($string)
{
    if (is_null($string)) {
        return;
    }

    return preg_replace(['/\./', '/\.\s+/'], ', ', $string);
}

function generateQrCode($number, $size)
{
    $qrcode = QrCode::format('png')
        ->size($size)
        ->margin(0)
        ->generate($number);

    return '<img src="data:image/png;base64, '.base64_encode($qrcode).'">';
}
