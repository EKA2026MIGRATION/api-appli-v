<?php

namespace App\Service;

use App\Entity\StatsAnalyseStrat;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\ProductService;
use DateTime;
use \PDO;


/**
 * StatistiqueService class
 * @author Sandy Razafitrimo <sandy@etsik.com>
 */
class StatistiqueService
{
    private $em;

    private $mainService;

    public function __construct(
        EntityManagerInterface $em,
        MainServiceInterface $mainService,
        ProductServiceInterface $productService
    ) {
        $this->em = $em;
        $this->mainService = $mainService;
        $this->productService = $productService;

    }

    public function getRepartition($data) {
        $data = json_decode($data, true);
        $dateStart = $data['dateStart'];
        $dateEnd   = $data['dateEnd'];

        $metaGroup = $this->productService->getMetaGroupCategory();
        $metaGroup['non classé'] = 'non classé';

        $dateStart = $dateStart.' 00:00:00';
        $dateEnd   = $dateEnd.' 23:59:59';

        $nbFamilys = [];
        $nbCategorys = [];
        $nbProducts = [];
        $nbTotal = 0;

        $caFamilys = [];
        $caCategorys = [];
        $caProducts = [];
        $caTotal = 0;

        $conn = $this->em->getConnection();

        $myQuery = "SELECT      i.invoice_id as invoice_id,
                                ip.name_fr as name,
                                ip.price_ttc,
                                f.name as family_name,
                                c.public_name as public_category,
                                c.name as category_name,
                                ip.suppressed as suppressed
                                
                       FROM invoice_product ip 
                       INNER JOIN invoice i ON i.invoice_id = ip.invoice_id
                       LEFT JOIN product p ON ip.name_fr = p.name_fr 
                       LEFT JOIN family f ON p.family_id = f.family_id
                       LEFT JOIN product_category_link pcl ON p.product_id = pcl.product_id  
                       LEFT JOIN category c ON pcl.category_id = c.category_id
                       
                       WHERE i.date BETWEEN :dateStart AND :dateEnd
                       AND i.status = 'payed'
                       ";

        $r = $conn->prepare($myQuery);
        $r->bindParam('dateStart', $dateStart);
        $r->bindParam('dateEnd', $dateEnd);
        $results = $r->executeQuery()->fetchAllAssociative();

        foreach($results as $result) {

            //if($result['suppressed'] == 1) continue;

            $product_name = html_entity_decode(trim(strip_tags($result['name'])));
            $family_name  = $result['family_name'];
            $public_category = $result['public_category'];


            if($family_name == "" || $public_category == "") {

                $product_search = '%'.$product_name.'%';
                $product_search2 = '%'.htmlentities($product_name, 0, 'UTF-8').'%';
                $origin_name     = '%'.$result['name'].'%';

                $myQuery = "SELECT              
                                        f.name as family_name,
                                        c.public_name as public_category,
                                        c.name as category_name
                                                
                                    FROM product p
                                    LEFT JOIN family f ON p.family_id = f.family_id
                                    LEFT JOIN product_category_link pcl ON p.product_id = pcl.product_id  
                                    LEFT JOIN category c ON pcl.category_id = c.category_id
                                    
                                    WHERE 
                                    p.name_fr like :product_name 
                                    OR p.name_fr like :product_name2
                                    OR p.name_fr like :origin_name
                                    LIMIT 1;
                                    ";

                        $r = $conn->prepare($myQuery);
                        $r->bindParam('product_name', $product_search);
                        $r->bindParam('product_name2', $product_search2);
                        $r->bindParam('origin_name', $origin_name);

                        $singleResult = $r->executeQuery()->fetchAssociative();

                $family_name  = $singleResult['family_name'];
                $public_category = $singleResult['public_category'];


            }


            if($family_name == "") {
                $family_name = "non classé";
                $product_without_family[] = $result['invoice_id'].' '.$product_name;
            }

            if($public_category == "") {
                $public_category = "non classé";
                $product_without_category[] = $result['invoice_id'].' '.$product_name;
            }
                 
                    
            if(!isset($nbFamilys[$family_name]))  {
                $nbFamilys[$family_name] = 0;
                $caFamilys[$family_name] = 0;

            }
            $nbFamilys[$family_name]++;
            $caFamilys[$family_name] += $result['price_ttc'];


            if(!isset($nbCategorys[$public_category]))  {
                $nbCategorys[$public_category] = 0;
                $caCategorys[$public_category] = 0;
            }

            $nbCategorys[$public_category]++;
            $caCategorys[$public_category] += $result['price_ttc'];
            
            
            if(!isset($nbProducts[$product_name])) {
                $nbProducts[$product_name] = 0;
                $caProducts[$product_name] = 0;
            }

            $nbProducts[$product_name]++;
            $caProducts[$product_name] += $result['price_ttc'];


            if(key_exists($public_category, $metaGroup)) {
                if(!isset($nbMetaGroup[$metaGroup[$public_category]])) {
                    $nbMetaGroup[$metaGroup[$public_category]] = 0;
                    $caMetaGroup[$metaGroup[$public_category]] = 0;
                }

                $nbMetaGroup[$metaGroup[$public_category]]++;
                $caMetaGroup[$metaGroup[$public_category]] += $result['price_ttc'];
            } else {
                if(!isset($nbMetaGroup['non classé'])) {
                    $nbMetaGroup['non classé']++;
                    $caMetaGroup['non classé'] += $result['price_ttc'];
                }
            }


            $nbTotal++;
            $caTotal += $result['price_ttc'];

        }

        ksort($nbFamilys);
        arsort($nbCategorys);
        ksort($nbProducts);
        ksort($caFamilys);
        arsort($caCategorys);
        ksort($caProducts);

        asort($product_without_family);
        asort($product_without_category);

        ksort($nbMetaGroup);
        ksort($caMetaGroup);

        return [
                'nbFamilys'    => $nbFamilys,
                'nbCategory'   => $nbCategorys,
                'nbMetaGroup'   => $nbMetaGroup,
                'nbProducts'   => $nbProducts,
                'nbTotal'      => $nbTotal,
                'caFamilys'    => $caFamilys,
                'caCategorys' => $caCategorys,
                'caMetagroup' => $caMetaGroup,
                'caProduct'    => $caProducts,
                'caTotal'      => $caTotal,
                'product_without_family' => $product_without_family,
                'product_without_category' => $product_without_category
            ];

    }


    public function findStringArrayKey($productsList, $searched) {
        $result = [];
        foreach($productsList as $origin => $value) {
            if(stristr($origin, $searched)) return true;
        }
        return false;
    }

    public function getStatCa($data)
    {
        $data = json_decode($data, true);

        $seasonRefId = $data['seasonRefId'];
        $dateStart   = $data['dateStart'];
        $dateEnd     = $data['dateEnd'];
        $weekName    = $data['weekName'];

        // get seasoin ref and weekref
        if(!$seasonRef = $this->em->getRepository('App\Entity\Season')->find($seasonRefId)) $seasonRef = null;
        if(!$weekRef= $this->em->getRepository('App\Entity\Week')->findOneBy(['season' => $seasonRef, 'name' => $weekName])) $weekRef = null;

        if($weekRef) {
            $dateStartRef = $weekRef->getDateStart()->format('Y-m-d');
            $dateEndRef   = $this->nextDay($dateStartRef, 7);
            $statDataRef  = $this->getStatData($dateStartRef, $dateEndRef);
            $resultRef    = $statDataRef['result'];
            $weekRef      = $statDataRef['week'];
        } else {
            $resultRef = null;
            $weekRef   = null;
        }
       
        $statData = $this->getStatData($dateStart, $dateEnd);
        $result = $statData['result'];
        $week   = $statData['week'];

        return ['result' => $result, 'resultRef' => $resultRef, 'week' => $week, 'weekRef' => $weekRef];
    }

    public function nextDay($date_ref, $n = 1)
    {
        return date('Y-m-d', strtotime($date_ref . ", +" . $n . " day"));
    }

    public function showDate($date, $format = 'd/m/Y')
    {

        $daysEn = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $daysFr = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];

        $myDate = new DateTime($date);
        $string = $myDate->format($format);

        return str_replace($daysEn, $daysFr, $string);
    }

    public function getMoment($presence)
    {
        // hours presence
        $start = $presence->getStart()->format('H:i');
        $end   = $presence->getEnd()->format('H:i');

        // moment
        $startRef = str_replace(':', '', $start);
        $endRef   = str_replace(':', '', $end);

        $moment = null;

        if ($startRef <= "1100" && $endRef <= "1300") {
            $moment = "am";
        }

        if ($startRef <= "1100" && $endRef >= "1500") {
            $moment = "day";
        }

        if ($startRef >= "1300" && $endRef >= "1500") {
            $moment = "pm";
        }


        if ($startRef >= "1100" && $endRef <= "1400") {
            $moment = "midi";
        }

        if ($startRef == "0000" || $endRef == "0000") {
            $moment = "day";
        }

        if (!$moment) {
            $moment = $startRef . '-' . $endRef;
        }

        return $moment;
    }




    public function getStatData($dateStart, $dateEnd) {

        $dateLimit = $dateEnd;
        $dateCurrent = $dateStart;

        $j = 0;
        $averageByMoment = [];

        $totalTtcWeek = 0;
        $totalHtWeek  = 0;
        $totalVatWeek = [];

        $totalTtcByLocationWeek = [];
        $totalHtByLocationWeek = [];
        $nbChildPresenceWeek = 0;
        $nbPresencesByLocationWeek = [];


        while ($dateCurrent != $dateLimit) {

            // date of current Day
            $result[$j]['date']   = $this->showDate($dateCurrent, 'l') . '<br/>' . $this->showDate($dateCurrent, 'd/m/y');

            if ($childPresences = $this->em->getRepository('App\Entity\ChildPresence')->findAllByDate($dateCurrent)) {


                $locations = [];
                $listAmountByLocationMoment = [];

                $nbChildPresence = 0;
                $nbPresencesByLocation = [];
                $nbPresencesByLocationByMoment = [];

                $totalTtcByDate = 0;
                $totalHtByDate  = 0;
                $totalVatByDate = [];

                $totalTtcByLocationByMoment = [];
                $totalTtcByLocation = [];
                $totalHtByLocation = [];
                $totalTtcByFamily = [];

                $demo = [];


                // create data by single date with childPresence information
                foreach ($childPresences as $presence) {

                    /**
                     * PART 1
                     * CREATE ALL DATAS
                     */

                    // location
                    $location = $presence->getLocation()->getName();
                    if (!in_array($location, $locations)) $locations[] = $location;


                    // moment
                    $moment = $this->getMoment($presence);

                    // registraton
                    if (!$registration = $presence->getRegistration()) {
                        $registration = null;
                        $registrationId = null;
                    } else {
                        $registrationId = $registration->getRegistrationId();
                    }

                    // product
                    if ($registration && $registration->getProduct()) {
                        $product = $registration->getProduct();
                        $product_name = strip_tags($product->getNameFr());
                    } else {
                        $product = null;
                        $product_name = null;
                    }


                    // % product
                    if($product) {

                        $components = [];
                        foreach ($product->getComponents() as $component) {
                            if (!$component->getSuppressed()) {
                              //  $components[] = $this->mainService->toArray($component->toArray());
                              
                              if(!isset($components['vat'][$component->getVat()])) $components['vat'][$component->getVat()] = 0;
                              $components['vat'][$component->getVat()] += $component->getTotalVat();
                              
                              if(!isset($components['totalTtc'])) $components['totalTtc'] = 0;
                              $components['totalTtc'] += $component->getTotalTtc();  
                                       
                            }
                        }
                         // % vat
                         $sumVat = array_sum($components['vat']);
                         $totalHt = $components['totalTtc'] - $sumVat;
                         if($components['totalTtc'] > 0) {
                            foreach($components['vat'] as $vat => $amount) {
                                $percentVat[$vat] = $amount*100 / $components['totalTtc'];
                            }
                            // total price HT
                            $percentHt = $totalHt * 100 / $components['totalTtc'];
                         }
                    } else {
                        $components = null;
                    }


                    // family
                    if ($product) {
                        $family_name = $product->getFamily()->getName();
                    } else {
                        $family_name = null;
                    }

                    // amountTtcTotal
                    $amountTtcTotal = null;
                    if ($registration && $product) {
                        // check paiement
                        if (!$registration->getPayed()) {
                            $amountTtcTotal = $product->getPriceTtc();
                        } else {
                            $amountTtcTotal = $registration->getPayed();
                        }
                    }


                    // amountTtcByDate
                    $amountTtcByPresence = null;
                    if ($amountTtcTotal && $registration && $registration->getSessions()) {

                        // nbDate by Product
                        $nbDateByProduct = 1;
                        if (null !== $product->getDates()) {
                            $allDates = array();
                            foreach ($product->getDates() as $myDate) {
                                if (null !== $myDate->getDate()) {
                                    $allDates[] = $myDate->getDate()->format('Y-m-d');
                                }
                            }
                            if (count($allDates) > 0) $nbDateByProduct = count($allDates);
                        }
                        $amountTtcByPresence = number_format($amountTtcTotal / $nbDateByProduct, 2, '.', '');
                    }

                    // calcul percent
                    if($components && $amountTtcByPresence && $percentHt > 0) {
                        $amountHtByPresence = number_format($amountTtcByPresence * $percentHt / 100, 2, '.', '');

                        foreach($percentVat as $vat => $perc) {
                            $amountVatByPresence[$vat] = number_format($amountTtcByPresence * $perc / 100, 2, '.', '');
                        }
                    } else {
                        $amountHtByPresence = 0;
                        $amountVatByPresence = null;
                        
                    }
                    
                    if(!isset($listAmountByLocationMoment[$location][$moment][$amountTtcByPresence])) $listAmountByLocationMoment[$location][$moment][$amountTtcByPresence] = 0;
                    $listAmountByLocationMoment[$location][$moment][$amountTtcByPresence]++;

                    /**
                     *  PART 2
                     *  CALCUL PRESENCE
                     * 
                     */


                    $nbChildPresence++;
                    $nbChildPresenceWeek++;

                    // presence by location
                    if (!isset($nbPresencesByLocation[$location])) $nbPresencesByLocation[$location] = 0;
                    $nbPresencesByLocation[$location]++;

                    if (!isset($nbPresencesByLocationWeek[$location])) $nbPresencesByLocationWeek[$location] = 0;
                    $nbPresencesByLocationWeek[$location]++;

                    // nb presence by location by moment 
                    if (!isset($nbPresencesByLocationByMoment[$location][$moment])) $nbPresencesByLocationByMoment[$location][$moment] = 0;
                    $nbPresencesByLocationByMoment[$location][$moment]++;

                    /**
                     *  PART 3
                     *  CALCUL TOTAL AND ORDER TOTAL TTC
                     * 
                     */


                    // total ttc by date
                    $totalTtcByDate += $amountTtcByPresence;
                    // ht
                    $totalHtByDate += $amountHtByPresence;
                    // ht by vat
                    if($amountVatByPresence) {
                        foreach($amountVatByPresence as $vat => $amount) {
                            if(!isset($totalVatByDate[$vat])) $totalVatByDate[$vat] = 0;
                            $totalVatByDate[$vat] += $amount;
                        }
                    }
                    


                    // total by location
                    if (!isset($totalTtcByLocation[$location])) $totalTtcByLocation[$location] = 0;
                    $totalTtcByLocation[$location] += $amountTtcByPresence;
                    // week value
                    if(!isset($totalTtcByLocationWeek[$location])) $totalTtcByLocationWeek[$location] = 0;
                    $totalTtcByLocationWeek[$location] += $amountTtcByPresence;


                    // total HT by location
                    if (!isset($totalHtByLocation[$location])) $totalHtByLocation[$location] = 0;
                    $totalHtByLocation[$location] += $amountHtByPresence;
                    // week value
                    if(!isset($totalHtByLocationWeek[$location])) $totalHtByLocationWeek[$location] = 0;
                    $totalHtByLocationWeek[$location] += $amountHtByPresence;


                    // total ttc by location by moment
                    if (!isset($totalTtcByLocationByMoment[$location][$moment])) $totalTtcByLocationByMoment[$location][$moment] = 0;
                    $totalTtcByLocationByMoment[$location][$moment] += $amountTtcByPresence;


                    // total by product family
                    if (!isset($totalTtcByFamily[$family_name])) $totalTtcByFamily[$family_name] = 0;
                    $totalTtcByFamily[$family_name] += $amountTtcByPresence;



                    // list all amount by location by moment
                    $amountTtcLocationMoment[$location][$moment][$amountTtcByPresence] = $amountTtcByPresence;

/*
                    $presenceResult[] = [
                        'registration_id' => $registrationId,
                        'location'     => $location,
                        'moment'       => $moment,
                        'timeStart'    => $presence->getStart()->format('H:i'),
                        'timeEnd'      => $presence->getEnd()->format('H:i'),
                        'family'       => $family_name,
                        'product_name' => $product_name,
                        'amount'       => $amountTtcByPresence,
                        'amountHt'     => $amountHtByPresence
                    ];*/
                }

                /**
                 * PART 5
                 * RE-ORDER DATA
                 */
                ksort($nbPresencesByLocation);

                // format data
                foreach($totalTtcByLocation as $loc => $value) {
                    $totalTtcByLocation[$loc] = number_format($value, 2, '.', '');
                }
                ksort($totalTtcByLocation);

                // week value
                foreach($totalTtcByLocationWeek as $loc => $value) {
                    $totalTtcByLocationWeek[$loc] = number_format($value, 2, '.', '');
                }
                ksort($totalTtcByLocationWeek);


                // format data
                foreach($totalHtByLocation as $loc => $value) {
                    $totalHtByLocation[$loc] = number_format($value, 2, '.', '');
                }
                ksort($totalHtByLocation);

                foreach($totalHtByLocationWeek as $loc => $value) {
                    $totalHtByLocationWeek[$loc] = number_format($value, 2, '.', '');
                }
                ksort($totalHtByLocationWeek);


                foreach($totalVatByDate as $vat => $value) {
                    $totalVatByDate[$vat] = number_format($value, 2, '.', '');
                }
                ksort($totalVatByDate);

                 // format data
                foreach($totalTtcByLocationByMoment as $loc => $values) {
                    foreach($values as $m => $val) {
                        $totalTtcByLocationByMoment[$loc][$m] = number_format($val, 2, '.', '');
                    }
                }

                  // format data
                foreach($totalTtcByFamily as $fam => $val) {
                        $totalTtcByFamily[$fam] = number_format($val, 2, '.', '');
                }

                ksort($totalTtcByFamily);

         

                /**
                 * PART 5
                 * CALCUL AVERAGE
                 */

                foreach ($totalTtcByLocation as $loc => $amount) {
                    if (isset($nbPresencesByLocation[$loc]) && $nbPresencesByLocation[$loc] > 0) {
                        $averageTtcByLocation[$loc] = number_format($amount / $nbPresencesByLocation[$loc], 2, '.', '');
                    } else {
                        $averageTtcByLocation[$loc] = "nbPresencesByLocation not found";
                    }
                }

                 $averageTtcLocMomentBasic = []; $averageTtcLocMomentProp = [];
                foreach($listAmountByLocationMoment as $loc => $locMomentData) {
                    foreach($locMomentData as $m => $values) {
                        ksort($values);
                        $listAmountByLocationMomentOrdered[$loc][$m] = $values;

                        $k = 0; $l = 0; $sum = 0; $sum2 = 0; 
                        foreach($values as $price => $nbPrice) {
                            $sum += intval($price);
                            $k++;

                            $sum2 += intval($price) * intval($nbPrice);
                            $l+= intval($nbPrice);
                        } 

                        if ($k > 0 && $l > 0) {
                            $averageTtcLocMomentBasic[$loc][$m] = number_format($sum / $k, 2, '.', '');
                            $averageTtcLocMomentProp[$loc][$m]  = number_format($sum2 / $l, 2, '.', '');
                        }

                    }
                } 


                // day average by day product
                foreach($locations as $loc) {
                    (isset($averageTtcLocMomentProp[$loc]['day']) && $averageTtcLocMomentProp[$loc]['day'] > 0) ? $nbDayByTtcDayAmount[$loc] = number_format($totalTtcByLocation[$loc] / $averageTtcLocMomentProp[$loc]['day'], 2, '.', '') : $nbDayByTtcDayAmount[$loc] = null;
                    (isset($averageTtcLocMomentProp[$loc]['day'] )) ? $refDay[$loc] = $averageTtcLocMomentProp[$loc]['day'] : $refDay[$loc] = null;
                }
                

                $nbPresences = [
                    'nbTotalPresences'     => $nbChildPresence,
                    'nbTotalByLocation'    => $nbPresencesByLocation,
                    'nbByLocationByMoment' => $nbPresencesByLocationByMoment,
                    'nbDayByTtcDayAmount'  => $nbDayByTtcDayAmount,
                    'amountRefDay'         => $refDay
                ];

                $total = [
                    'totalTtcByDate'                => number_format($totalTtcByDate, 2, '.', ''),
                    'totalHtByDate'                 => number_format($totalHtByDate, 2, '.', ''),
                    'totalVatByDate'                => $totalVatByDate,
                    'totalTtcByLocation'            => $totalTtcByLocation,
                    'totalTtcByFamily'              => $totalTtcByFamily,
                    'totalTtcByLocationByMoment'    => $totalTtcByLocationByMoment,
                ];

                $average = [
                    'listAmountByLocationMoment'    => $listAmountByLocationMomentOrdered,
                    'averageTtcByLocation'          => $averageTtcByLocation,
                    'averageTtcLocMomentBasic'      => $averageTtcLocMomentBasic,
                    'averageTtcLocMomentProp'       => $averageTtcLocMomentProp

                ];
      

                // result datas
                $result[$j]['hasData']   = true;
                $result[$j]['locations'] = $locations;
                $result[$j]['presences'] = $nbPresences;
                $result[$j]['total']     = $total;
                $result[$j]["averageTotal"] = $average;


                //$result[$j]['data'] = $presenceResult;


                // increment totalWeek
                $totalTtcWeek += floatval($totalTtcByDate);
                $totalHtWeek  += floatval($totalHtByDate);

                foreach($totalVatByDate as $vat => $value) {
                    if(!isset($totalVatWeek[$vat])) $totalVatWeek[$vat] = 0;
                    $totalVatWeek[$vat] += $value;
                }
                ksort($totalVatWeek);

                foreach($totalVatWeek as $vat => $value) {
                    $totalVatWeek[$vat]= number_format($value, 2, '.', '');
                }


            

                // there is no childPresence on currentDay$totalVatByDate
            } else {
                $result[$j]['hasData']  = false;
            }


            // increment day
            $dateCurrent = $this->nextDay($dateCurrent);
            $j = $j + 1;
        };


        $week = [
            'totalTtcWeek' => number_format($totalTtcWeek, 2, '.', ''),
            'totalHtWeek'  => number_format($totalHtWeek, 2, '.', ''),
            'totalVatWeek' => $totalVatWeek,
            'totalTtcByLocationWeek' => $totalTtcByLocationWeek,
            'totalHtByLocationWeek' => $totalHtByLocationWeek,
            'nbPresencesByLocationWeek' => $nbPresencesByLocationWeek,
            'nbChildPresenceWeek'       => $nbChildPresenceWeek,
                         'demo' => $demo

        ];

        return ['result' => $result, 'week' => $week];

    }

    public function getEstimation($month, $year) {

        /** INIT DATAS */

        $result = []; $allDatas = ['history' => [], 'current' => []];
        $conn = $this->em->getConnection();

        /** CREATE SEASON DATES WEEKS FOR CURRENT AND HISTORY */

        //create dateStart and dateEnd
        $dateStart = new DateTime($year.'-'.$month.'-01');
        $dateEnd   = new DateTime($year.'-'.$month.'-01');
        $dateEnd->modify('last day of this month');

        // current Season where suppressed = 0, status = active and dateStart is "like" the current month-year
        $season = $this->em->getRepository('App\Entity\Season')->findOneIncludeMonth($dateStart->format('Y-m-d'));
        $dateStartMonday = $dateStart->format('N') == 1 ? $dateStart->format('Y-m-d') : (clone $dateStart)->modify('last monday')->format('Y-m-d');
        $dateEndMonday  = $dateEnd->format('N') == 1 ? $dateEnd->format('Y-m-d') : (clone $dateEnd)->modify('last monday')->format('Y-m-d');

        $allDatas['current']['season'] = [
            'dateStart' => $dateStart->format('Y-m-d').' 00:00:00',
            'dateEnd'   => $dateEnd->format('Y-m-d').' 23:59:59',
            'dateStartMonday' => $dateStartMonday,
            'dateEndMonday'   => $dateEndMonday,
            'name'      => $season->getName()
        ];

        $add = 0; $currentWeeks = [];
        foreach($season->getWeeks() as $week) {
            $dateStartWeek = $week->getDateStart()->format('Y-m-d');
            if($dateStartMonday == $dateStartWeek) $add = 1;
            if( $add == 1) {
                $dateEndWeek = (new DateTime($dateStartWeek))->modify('next Sunday')->format('Y-m-d');
                $currentWeeks[$week->getName()] = ['from' => $dateStartWeek.' 00:00:00', 'to' => $dateEndWeek.' 23:59:59', 'kind' => $week->getKind()];
            }
            if( $dateStartWeek == $dateEndMonday) $add = 0;
        }
        $allDatas['current']['season']['weeks'] = $currentWeeks;

        // get previous season with these critera date_start < startDate, suppressed = 0 order by date_start desc limit 1
        $previousSeason = $this->em->getRepository('App\Entity\Season')
            ->createQueryBuilder('s')
            ->where('s.dateStart < :dateStart')
            ->andWhere('s.suppressed = 0')
            ->setParameter('dateStart', $season->getDateStart())
            ->orderBy('s.dateStart', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        $previousWeeks = []; $i = 0;

        if($previousSeason) {
            foreach ($previousSeason->getWeeks() as $preWeek) {
                $thisName = trim($preWeek->getName());
                if(key_exists($thisName, $currentWeeks)) {
                    if($i == 0) $previousStartDate = $preWeek->getDateStart()->format('Y-m-d');;
                    $to = (new DateTime($preWeek->getDateStart()->format('Y-m-d')))->modify('next Sunday')->format('Y-m-d');
                    $previousWeeks[$thisName] = ['from' => $preWeek->getDateStart()->format('Y-m-d').' 00:00:00', 'to' => $to.' 23:59:59', 'kind' => $preWeek->getKind()];
                    $i++;
                    $previousEndDate = $preWeek->getDateStart()->format('Y-m-d');
                }
            };
            $dateEnd = (new DateTime($previousEndDate))->modify('this Sunday')->format('Y-m-d');
            $allDatas['history']['season'] = [
                'dateStart' => $previousStartDate.' 00:00:00',
                'dateEnd' => $dateEnd.' 23:59:59',
                'dateStartMonday' => $previousStartDate,
                'dateEndMonday'   => $previousEndDate,
                'name'      => $previousSeason->getName(),
                'weeks'     => $previousWeeks
            ];
        }

        /**   START STATS */


        /** 1 type of family product by registration */

        // HISTORY

        // current
        $sql = "SELECT r.registration, f.name, p.name_fr as name_product, l.name as location, p.lunch, p.transport
                FROM registration r
                INNER JOIN product p ON p.product_id = r.product_id
                INNER JOIN family f ON f.family_id = p.family_id
                INNER JOIN location l ON l.location_id = r.location_id
                WHERE r.registration >= :from and r.registration < :to
                AND r.suppressed = 0 ";
        $stmt = $conn->prepare($sql);
        $historyRegistrations = $stmt->executeQuery([':from' => $allDatas['current']['season']['dateStart'], ':to' => $allDatas['current']['season']['dateEnd']])->fetchAllAssociative();

        $allDatas['current']['registration'] = $this->analyzeRegistrations($historyRegistrations);



        // history
        $sql = "SELECT r.registration, f.name, p.name_fr as name_product, l.name as location, p.lunch, p.transport
                FROM registration r
                INNER JOIN product p ON p.product_id = r.product_id
                INNER JOIN family f ON f.family_id = p.family_id
                INNER JOIN location l ON l.location_id = r.location_id
                WHERE r.registration >= :from and r.registration < :to
                AND r.suppressed = 0 ";
        $stmt = $conn->prepare($sql);
        $historyRegistrations = $stmt->executeQuery([':from' => $allDatas['history']['season']['dateStart'], ':to' => $allDatas['history']['season']['dateEnd']])->fetchAllAssociative();

        $allDatas['history']['registration'] = $this->analyzeRegistrations($historyRegistrations);


        /** 2 chlld presence */

        // during the currnet month
        $sql = '
        SELECT cp.date, cp.start, cp.end, cp.status, c.birthdate, TIMESTAMPDIFF(YEAR, c.birthdate, CURDATE()) AS age, l.name as location 
        FROM child_presence cp
        INNER JOIN child c ON c.child_id = cp.child_id
        INNER JOIN location l ON l.location_id = cp.location_id
        WHERE cp.suppressed = 0
        AND cp.date >= :from and cp.date <= :to
        ORDER BY cp.date asc;
        ';

        $stmt = $conn->prepare($sql);
        $childPresences = $stmt->executeQuery([':from' => $allDatas['current']['season']['dateStart'], ':to' => $allDatas['current']['season']['dateEnd']])->fetchAllAssociative();

        $allDatas['current']['presences'] = $this->analyzeChildPresences($childPresences, $allDatas['current']['season']['weeks']);

        // during the month year before
        $sql = '
        SELECT cp.date, cp.start, cp.end, cp.status, c.birthdate, TIMESTAMPDIFF(YEAR, c.birthdate, CURDATE()) AS age, l.name as location 
        FROM child_presence cp
        INNER JOIN child c ON c.child_id = cp.child_id
        INNER JOIN location l ON l.location_id = cp.location_id
        WHERE cp.suppressed = 0
        AND cp.date >= :from and cp.date <= :to
        ORDER BY cp.date asc;
        ';

        $stmt = $conn->prepare($sql);
        $childPresences = $stmt->executeQuery([':from' => $allDatas['history']['season']['dateStart'].' 00:00:00', ':to' => $allDatas['history']['season']['dateEnd'].' 23:59:59'])->fetchAllAssociative();

        $allDatas['history']['presences'] = $this->analyzeChildPresences($childPresences, $allDatas['history']['season']['weeks'], true);



        /** 3 activity & sport & lunch */
        $sql = "
        SELECT pa.date, s.name, TIMESTAMPDIFF(YEAR, c.birthdate, CURDATE()) AS age
        FROM pickup_activity pa
        INNER JOIN sport s ON s.sport_id = pa.sport_id
        INNER JOIN child c ON c.child_id = pa.child_id
        WHERE pa.date >= :from AND pa.date <= :to";

        $stmt = $conn->prepare($sql);
        $childActivitys = $stmt->executeQuery([':from' => $allDatas['history']['season']['dateStart'].' 00:00:00', ':to' => $allDatas['history']['season']['dateEnd'].' 23:59:59'])->fetchAllAssociative();

        $allDatas['history']['activitys'] = $this->analyzeActivityPresences($childActivitys, $allDatas['history']['season']['weeks'], true);

        $sql = "
        SELECT pa.date, s.name, TIMESTAMPDIFF(YEAR, c.birthdate, CURDATE()) AS age
        FROM pickup_activity pa
        INNER JOIN sport s ON s.sport_id = pa.sport_id
        INNER JOIN child c ON c.child_id = pa.child_id
        WHERE pa.date >= :from AND pa.date <= :to";

        $stmt = $conn->prepare($sql);
        $childActivitys = $stmt->executeQuery([':from' => $allDatas['current']['season']['dateStart'].' 00:00:00', ':to' => $allDatas['current']['season']['dateEnd'].' 23:59:59'])->fetchAllAssociative();

        $allDatas['current']['activitys'] = $this->analyzeActivityPresences($childActivitys, $allDatas['history']['season']['weeks'], true);

        return $allDatas;
    }

    public function analyzeRegistrations($registrations) {

        // data
        $nameProductCount = [];
        $nameCount = [];
        $locationCount = [];
        $hasLunchCount = 0;
        $hasTransportCount = 0;
        $totalRegistrations = count($registrations);

        foreach($registrations as $key => $registration) {

            $nameProduct = trim(html_entity_decode(strip_tags($registration['name_product'])));
            $registration[$key]['name_product'] = $nameProduct;

            // count all

            // Comptage de name_product
            if (!isset($nameProductCount[$nameProduct])) $nameProductCount[$nameProduct] = 0;
            $nameProductCount[$nameProduct]++;

            // Comptage de name
            $name = $registration['name'];
            if (!isset($nameCount[$name])) $nameCount[$name] = 0;
            $nameCount[$name]++;

            // Comptage de location
            $location = $registration['location'];
            if (!isset($locationCount[$location])) $locationCount[$location] = 0;
            $locationCount[$location]++;

            // Comptage de has_lunch et has_transport
            if ($registration['lunch']) $hasLunchCount++;
            if ($registration['transport']) $hasTransportCount++;
        }


        return [
            'totalByProduct' => $nameProductCount,
            'totalByFamily' => $nameCount,
            'totalByLocation' => $locationCount,
            'totalHasLunch' => $hasLunchCount,
            'totalHasTransport' => $hasTransportCount,
            'totalRegisration' => $totalRegistrations
        ];
    }


    public function analyzeActivityPresences($childActivitys, $weeks) {
        $ageGroups = [
            '<3' => [],
            '3-6' => [],
            '7-9' => [],
            '10-12' => [],
            '13-15' => [],
            '>15' => []
        ];
        $activitiesByKind = [];

        foreach ($weeks as $weekInfo) {
            $activitiesByKind[$weekInfo['kind']] = 0;
        }

        foreach ($childActivitys as $activity) {

            // Comptage par tranche d'âge et par sport
            $age = (int)$activity['age'];
            $sport = $activity['name'];

            $ageGroupKey = '';
            if ($age < 3) {
                $ageGroupKey = '<3';
            } elseif ($age >= 3 && $age <= 6) {
                $ageGroupKey = '3-6';
            } elseif ($age >= 7 && $age <= 9) {
                $ageGroupKey = '7-9';
            } elseif ($age >= 10 && $age <= 12) {
                $ageGroupKey = '10-12';
            } elseif ($age >= 13 && $age <= 15) {
                $ageGroupKey = '13-15';
            } elseif ($age > 15) {
                $ageGroupKey = '>15';
            }

            if (!isset($ageGroups[$ageGroupKey][$sport])) {
                $ageGroups[$ageGroupKey][$sport] = 0;
            }
            $ageGroups[$ageGroupKey][$sport]++;

            // Comptage par tranche d'âge
            $age = (int)$activity['age'];
            if ($age < 3) {
                $ageGroups['<3']++;
            } elseif ($age >= 3 && $age <= 6) {
                $ageGroups['3-6']++;
            } elseif ($age >= 7 && $age <= 9) {
                $ageGroups['7-9']++;
            } elseif ($age >= 10 && $age <= 12) {
                $ageGroups['10-12']++;
            } elseif ($age >= 13 && $age <= 15) {
                $ageGroups['13-15']++;
            } elseif ($age > 15) {
                $ageGroups['>15']++;
            }

            // Comptage par type de semaine (kind)
            $activityDate = new DateTime($activity['date']);
            foreach ($weeks as $weekName => $weekInfo) {
                $from = new DateTime($weekInfo['from']);
                $to = new DateTime($weekInfo['to']);

                if ($activityDate >= $from && $activityDate <= $to) {
                    $activitiesByKind[$weekInfo['kind']]++;
                    break;
                }
            }
        }

        return [
            'Par groupe d\'âge' => $ageGroups,
            'Par type de produit' => $activitiesByKind
        ];
    }


    public function analyzeChildPresences($childPresences, $weeks) {
        $totalCount = count($childPresences);
        $locationCount = [];
        $ageGroups = ['<3' => 0, '3-6' => 0, '7-9' => 0, '10-12' => 0, '13-15' => 0, '>15' => 0];
        $sessionTypes = ['Matinée' => 0, 'Après-midi' => 0, 'Journée' => 0, '1 à 2h' => 0];

        $weekCounts = [];
        $kindCounts = [];
        $absences = [];

        foreach ($weeks as $weekName => $weekInfo) {
            $weekCounts[$weekName] = 0;
            $kindCounts[$weekInfo['kind']] = 0;
        }

        foreach ($childPresences as $presence) {
            // Compter les lieux différents
            if (!isset($locationCount[$presence['location']])) {
                $locationCount[$presence['location']] = 0;
            }
            $locationCount[$presence['location']]++;

            // Calculer l'âge et le grouper
            $age = (int)$presence['age'];
            if ($age < 3) {
                $ageGroups['<3']++;
            } elseif ($age >= 3 && $age < 7) {
                $ageGroups['3-6']++;
            } elseif ($age >= 7 && $age < 10) {
                $ageGroups['7-9']++;
            } elseif ($age >= 10 && $age < 13) {
                $ageGroups['10-12']++;
            } elseif ($age >= 13 && $age <= 15) {
                $ageGroups['13-15']++;
            } elseif ($age > 15) {
                $ageGroups['>15']++;
            }

            // Calculer le type de session
            $startTime = DateTime::createFromFormat('H:i:s', $presence['start']);
            $endTime = DateTime::createFromFormat('H:i:s', $presence['end']);
            $duration = $startTime->diff($endTime);

            if ($startTime <= new DateTime('10:00:00') && $endTime >= new DateTime('16:00:00')) {
                $sessionTypes['Journée']++;
            } elseif ($endTime < new DateTime('13:00:00')) {
                $sessionTypes['Matinée']++;
            } elseif ($startTime >= new DateTime('13:00:00')) {
                $sessionTypes['Après-midi']++;
            } elseif ($duration->h < 2) {
                $sessionTypes['1 à 2h']++;
            }

            // compter les absences
            if($presence['status'] == 'npec') {
                if(!isset($absences['total']))  $absences['total'] = 0;
                $absences['total']++;
            }

            // compter les présences par semaine
            foreach ($weeks as $weekName => $weekInfo) {
                $presenceDate = new DateTime($presence['date']);
                $from = new DateTime($weekInfo['from']);
                $to = new DateTime($weekInfo['to']);

                if ($presenceDate >= $from && $presenceDate <= $to) {
                    $weekCounts[$weekName]++;
                    $kindCounts[$weekInfo['kind']]++;
                    if($presence['status'] == 'npec') {
                        if(!isset($absences[$weekInfo['kind']])) $absences[$weekInfo['kind']] = 0;
                        $absences[$weekInfo['kind']]++;
                    }
                    break;
                }
            }
        }

        // Résultats
        return [
            'Total' => $totalCount,
            'Par lieu' => $locationCount,
            "Par groupe d'âge" => $ageGroups,
            'Par moment' => $sessionTypes,
            'Par semaine' => $weekCounts,
            'Par type de produit' => $kindCounts,
            'Nombre d\'Absences'  => $absences
        ];
    }

    public function updateAnalyseStrat($elements) {
        $elements = json_decode($elements, true);

        return $elements;
    }

    public function getReenrollment($season_id, $type, $groupName = null) {

        // current element
        $season = $this->em->getRepository('App\Entity\Season')->findOneBy(['seasonId' => $season_id]);
        if(!$season) return ['message' => 'Season not found'];

        $current = [];
        $previous = [];
        $allGroupNames = [];
        $arr = [];

        if($type == "trimestre") {
            // get date from to with $groupName
            $turn = 0; $latest = null;
            foreach($season->getWeeks() as $week) {
                $arr[] = $week->toArray();

                if(!key_exists($week->getGroupName(), $allGroupNames)) {
                    $start = $week->getDateStart()->format('Y-m-d').' 00:00:00';
                    $date = new DateTime($start);
                    $date->modify('next sunday');
                    $end = $date->format('Y-m-d').' 23:59:59';
                    $allGroupNames[$week->getGroupName()] = ['name' => $week->getGroupName(), 'start' => $start, 'end' => $end];
                } else {
                    $allGroupNames[$week->getGroupName()]['end'] = $week->getDateStart()->format('Y-m-d').' 23:59:59';
                }

                if($week->getGroupName() == $groupName) {
                    if($turn == 0) {
                        $current['start'] = $week->getDateStart()->format('Y-m-d').' 00:00:00';
                        $turn = 1;
                    }
                    $latest = $week->getDateStart()->format('Y-m-d H:i:s');
                }
            }
            if ($latest) {
                $date = new DateTime($latest);
                $date->modify('next sunday');
                $current['end'] = $date->format('Y-m-d').' 23:59:59';
            }
            $allGroupNames = array_values($allGroupNames);
        }

        return ['current' => $current, 'allGroupNames' => $allGroupNames, 'latest' => $latest, 'arr' => $arr ];


        // previous element

        // current date

        // previous date


        // get list of child in previous (from date to )


        // get list of chilf in current (form date to)


        // differenciel children
        

        // by child
        // - number of presence
        // - CA
        // - type of activite (ex: tennis, foot)
        //  - age group
        //  - coach (% presence with coach, % transport by coach, sum of child by coach)
        //  - postal code by child

    }

}
