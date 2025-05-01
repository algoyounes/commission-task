# Commission Calculator App

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

## Commission Logic & Architecture
- **Commission rules** _(deposit and withdraw, private vs. business)_ are implemented as separate classes under `Service/Commission/Rules`
- **Weekly free allowance** for private withdrawals is tracked in memory by `WithdrawalTrackerService`
- **Currency conversion** uses a resilient HTTP client _(AbstractHttpService)_ with retry/backoff
- **Precision math** is handled via a thin `Support/Math` wrapper around BC Math functions

## Libraries & Tools
- **PHP dotenv**: Loads environment variables from `.env` files
- **PHP BC Math**: Handles precise calculations
- **Guzzle**: PHP HTTP client with exponential backoff for failed requests
- **Mockery**: Mock object framework for testing
- **PHPUnit**: Unit testing framework
- **PHP CS Fixer**: Ensures code formatting and standards


