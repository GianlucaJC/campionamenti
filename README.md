# Campionamenti - Demo iniziale

Applicazione Laravel per la gestione dei controlli microbiologici.

Questa prima demo introduce una struttura dinamica: sezioni e punti di campionamento sono gestiti da database, non hardcoded nel form.

## Obiettivo

- Gestire piu sezioni di monitoraggio microbiologico.
- Gestire punti di campionamento dinamici per ogni sezione.
- Permettere aggiunte/riordino punti senza modificare il codice del form.
- Salvare controlli e risultati punto-per-punto.

## Stato attuale demo

- Form dinamico per sezione disponibile sulla route principale.
- Salvataggio di un controllo per singola sezione.
- Seeder iniziale con sezioni e punti principali derivati dal legacy.

## Avvio rapido

Prerequisiti:

- PHP 8.1+
- Composer
- Node.js (solo per asset frontend)
- Database configurato nel file .env

Comandi principali:

1. composer install
2. cp .env.example .env
3. php artisan key:generate
4. php artisan migrate --seed
5. npm install
6. npm run dev
7. php artisan serve

## Struttura dati (core)

- monitoring_sections: anagrafica sezioni
- sampling_points: anagrafica punti campionamento (ordinabili)
- microbiological_checks: testata controllo per sezione/data
- microbiological_check_points: risultati per singolo punto

## Note

- Per aggiungere punti prima/dopo altri, aggiornare sort_order in sampling_points.
- Le sezioni/punti attivi sono filtrati con il flag is_active.
