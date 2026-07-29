# Campionamenti

Applicazione Laravel per la gestione dei controlli microbiologici.

Le sezioni, i reparti e i punti di campionamento sono gestiti da database e non sono hardcoded nei form.

## Obiettivo

- Gestire piu sezioni di monitoraggio microbiologico.
- Gestire punti di campionamento dinamici per ogni sezione.
- Permettere aggiunte/riordino punti senza modificare il codice del form.
- Salvare controlli e risultati punto-per-punto.
- Tracciare firme, riaperture, eliminazioni e ripristini.

## Stato attuale demo

- Form dinamico per sezione disponibile sulla route principale.
- Archivio con filtri data e paginazione server-side.
- Gestione amministrativa di sezioni, reparti e punti di campionamento.
- Fasi di produzione con firme e tracciamento delle operazioni.
- Seeder iniziale con sezioni e punti principali derivati dal legacy.

## Ruoli

- `operatore`: compila e modifica i campionamenti, consulta l'archivio ed elimina logicamente i campionamenti.
- `admin`: consulta l'archivio completo, gestisce la struttura di monitoraggio e ripristina i campionamenti eliminati.

## Punti di campionamento

Dal pannello admin, il parametro richiesto dipende dal tipo di campionamento:

| Tipo | Parametro disponibile |
| --- | --- |
| Aria passiva | Tempo di esposizione: 3 o 4 ore |
| Aria attiva | Volume libero in litri |
| Superficie contact plate | Nessun parametro volume/tempo |
| Superficie swab | Nessun parametro volume/tempo |

Il sistema salva solo il parametro compatibile con il tipo selezionato.

## Archivio e audit

- I campionamenti eliminati usano il soft delete: non vengono rimossi fisicamente dal database.
- Un campionamento senza firme puo essere eliminato direttamente da un operatore.
- Un campionamento con una o piu firme puo essere eliminato solo inserendo una motivazione obbligatoria.
- Ogni eliminazione e ripristino viene registrato in `microbiological_check_phase_logs`, con utente, data/ora e, quando richiesta, motivazione.
- L'admin puo selezionare `Eliminati` nel filtro Stato dell'archivio e ripristinare il campionamento.

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
4. php artisan migrate
5. php artisan db:seed --class=MonitoringTemplateSeeder
6. npm install
7. npm run dev
8. php artisan serve

Per aggiornare un'installazione esistente dopo il pull delle modifiche:

```bash
php artisan migrate
php artisan optimize:clear
```

## Struttura dati (core)

- monitoring_sections: anagrafica sezioni
- monitoring_departments: reparti associati alle sezioni
- sampling_points: anagrafica punti campionamento (ordinabili)
- microbiological_checks: testata controllo per sezione/data
- microbiological_check_points: risultati per singolo punto
- microbiological_check_phase_states: stato delle firme delle fasi
- microbiological_check_phase_logs: tracciamento delle fasi e delle operazioni di archivio

## Note

- Per aggiungere punti prima/dopo altri, aggiornare sort_order in sampling_points.
- Le sezioni/punti attivi sono filtrati con il flag is_active.
- Il seeder non rimuove dati gia presenti: per eliminare sezioni non piu previste, usare la gestione admin oppure una migrazione dedicata.
