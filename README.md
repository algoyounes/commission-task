# Commission Calculator Task

> [!NOTE]
> This calculator requires PHP 8.2+

## Installation

1. **Bring up PHP container (optional)**
    ```bash
    make setup
    ```
2. **Install dependencies**

    ```bash
    composer install && chmod +x artisan
    ```
   
3. **Copy & configure your `.env`**
   ```bash
    cp .env.example .env
    # Edit .env, set EXCHANGE_RATES_API_KEY (e.g. 1a24e49fd554a882621cbf7edd718bd7)
   ```

## Usage

### Calculate commissions from a CSV

To calculate commissions from a CSV file, run the following command:

```bash
./artisan input.csv
```

Example Output:
```
➜ ./artisan input.csv
0.60
3.00
0.00
0.06
1.50
0
0.55
0.30
0.30
3.00
0.00
0.00
8508
```

### Run Tests

Run unit tests using the following command:

```bash
composer test:unit
```

## Commission Logic
- **Commission rules** _(deposit and withdraw, private vs business)_ are implemented as separate classes under `Services/Commission/Rules`
- **Weekly free allowance** for private withdrawals is tracked in memory by `WithdrawalTrackerService`
- **Currency conversion** uses a resilient HTTP client `AbstractHttpService` with `retry/backoff`
- **Precision math** is handled via a thin `Support/Math` wrapper around BC Math for accurate decimal operations

## Libraries
- **[PHP dotenv](https://github.com/vlucas/phpdotenv)** : Loads environment variables from `.env` files
- **[PHP BC Math](https://www.php.net/manual/en/book.bc.php)** : Handles precise calculations for floating-point math
- **[Guzzle](https://github.com/guzzle/guzzle)** : PHP HTTP client with exponential backoff for failed requests
- **[Mockery](https://github.com/mockery/mockery)** : Mocking dependencies to keep tests isolated
- **[PHPUnit](https://github.com/sebastianbergmann/phpunit)** : Writing and executing unit tests
- **[PHP CS Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer)** : Ensures code formatting and standards

