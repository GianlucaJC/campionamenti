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

- `operatore`: compila e modifica i campionamenti e consulta l'archivio.
- `admin`: consulta l'archivio completo, gestisce la struttura e le anagrafiche, elimina le sessioni e ripristina i campionamenti eliminati.

## Punti di campionamento

Dal pannello admin, il parametro richiesto dipende dal tipo di campionamento:

| Tipo | Parametro disponibile |
| --- | --- |
| Aria passiva | Tempo di esposizione: 3 o 4 ore |
| Aria attiva | Volume libero in litri |
| Superficie contact plate | Nessun parametro volume/tempo |
| Superficie swab | Nessun parametro volume/tempo |

Il sistema salva solo il parametro compatibile con il tipo selezionato.

Per i punti Acque l'admin puo indicare il locale e selezionare Aspetto, pH, R2A, Coliformi, Pseudomonas ed Enterococchi. Le analisi sono tutte selezionate per i nuovi punti e per quelli preesistenti senza configurazione; e possibile deselezionarle anche tutte. L'operatore vede solo le analisi abilitate: il risultato UFC/ml e calcolato dividendo per 100 le UFC/piastra per R2A o le UFC confermate per le altre analisi. Le date di fine incubazione sono registrate per punto e analisi. I risultati storici UFC/100 ml rimangono separati.

Nell'intestazione della sessione Acque si impostano i lotti di membrana e sterilizzazione e i lotti e le scadenze dei quattro terreni. Questi dati sono condivisi tra le sezioni; gli altri dati della scheda di campionamento rimangono specifici della sezione.

Nella vista admin `Anagrafiche operatori` si possono aggiungere ed eliminare divise, operatori campionati e reparti. I nuovi reparti copiano punti e configurazione da un reparto scelto. Le voci gia utilizzate in registrazioni o intestazioni non possono essere eliminate.

## Archivio e audit

- L'archivio mantiene la paginazione per data. In Produzione, Clean room e Operatori, ogni sessione della stessa data ha una scheda distinta con reparti, punti, stato delle firme e link di modifica propri.
- I campionamenti storici senza identificativo di sessione sono mostrati separatamente, senza unirli ad altri campionamenti della stessa data.
- L'admin puo eliminare logicamente un'intera sessione da `Gestione amministrativa`; se almeno una sezione e firmata la motivazione e obbligatoria.
- Le eliminazioni logiche e i ripristini sono registrati in `microbiological_check_phase_logs`. Le sezioni eliminate si possono ripristinare dal filtro `Eliminati`.
- Nel filtro `Eliminati`, l'admin puo anche eliminare definitivamente un'intera sessione dopo conferma: risultati, letture, firme e tracciamenti vengono rimossi senza possibilita di ripristino. La cancellazione definitiva e bloccata se la sessione contiene sezioni ancora attive.

## Trend

Nel pannello admin, la vista `Trend` consente di selezionare una o piu macro sezioni (`Produzione`, `Clean room`, `Acque`, `Operatori`), i relativi punti di campionamento e un intervallo date.

- Il grafico riporta i valori nel tempo con asse X sulle date di campionamento e asse Y sui valori rilevati.
- Le letture e i parametri disponibili sono mostrati come serie separate, selezionabili dalla legenda.
- Il grafico corrente puo essere esportato in PDF dal browser.

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
6. php artisan storage:link
7. npm install
8. npm run dev
9. php artisan serve

Per aggiornare un'installazione esistente dopo il pull delle modifiche:

```bash
php artisan migrate
php artisan storage:link
php artisan optimize:clear
```

## Foto dei reparti di Produzione

In `Gestione reparti`, l'admin puo caricare una foto JPEG, PNG o WebP (massimo 5 MB) per ogni reparto di Produzione. La foto viene mostrata agli operatori nella relativa scheda durante il nuovo campionamento.

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
