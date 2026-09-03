<?php
use App\Entity\Photographer;

require_once __DIR__ . '/vendor/autoload.php';

$kernel = new App\Kernel('dev', true);
$kernel->boot();

$entityManager = $kernel->getContainer()->get('doctrine')->getManager();

$extraPhotographers = [
    ['Akdeniz Masalı', 'Düğün', 'Antalya', 18000],
    ['Bursa Dış Çekim', 'Portre', 'Bursa', 8500],
    ['Bodrum Studio', 'Moda', 'Muğla', 22000],
    ['Karadeniz Kareleri', 'Düğün', 'Trabzon', 12000],
    ['Çukurova Stüdyo', 'Ürün', 'Adana', 9500],
];

foreach ($extraPhotographers as $data) {
    $p = new Photographer();
    $p->setName($data[0]);
    $p->setStyle($data[1]);
    $p->setCity($data[2]);
    $p->setPrice($data[3]);
    $entityManager->persist($p);
}

$entityManager->flush();
echo "Yeni şehir verileri başarıyla eklendi!\n";