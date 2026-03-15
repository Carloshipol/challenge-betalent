# Multi-Gateway Payment API

RESTful API built with **Laravel** implementing a **multi-gateway
payment system with automatic fallback and asynchronous processing**.

Developed as part of the **BeTalent Backend Challenge**.

------------------------------------------------------------------------

## Overview

This project implements a **payment processing API** capable of using
multiple payment gateways following a **priority strategy**.

If the first gateway fails, the system automatically attempts the next
available gateway.

Payments are processed **asynchronously using Laravel Jobs and Queues**,
allowing the API to respond quickly while the payment is processed in
the background.

------------------------------------------------------------------------

## Technology Stack

### Backend

-   PHP 8.4
-   Laravel 12
-   MySQL 8
-   Laravel Sanctum
-   Laravel Queue / Jobs

### Infrastructure

-   Docker
-   Docker Compose

### Testing

-   PHPUnit
-   Laravel Test Suite

------------------------------------------------------------------------

## Architecture

The application follows a **layered architecture**, separating
responsibilities between controllers, jobs, services, and gateway
integrations.

### Flow

    Controller → Job → PaymentService → Gateway

This separation allows:

-   Better **maintainability**
-   Clear **separation of concerns**
-   Easier **testing and scalability**

------------------------------------------------------------------------

## Payment Flow

1.  Client sends purchase request
2.  API creates a transaction with status **pending**
3.  `ProcessPayment` Job is dispatched
4.  Queue worker executes `PaymentService`
5.  Gateways are called in order of **priority**
6.  If one fails, the next gateway is attempted
7.  Transaction status is updated

------------------------------------------------------------------------

## Database Structure

### users

  Field      Description
  ---------- --------------------------
  email      User email
  password   User password
  role       User role (ADMIN / USER)

### gateways

  Field       Description
  ----------- --------------------------------
  name        Gateway name
  is_active   Indicates if gateway is active
  priority    Gateway execution priority

### clients

  Field   Description
  ------- --------------
  name    Client name
  email   Client email

### products

  Field    Description
  -------- ---------------
  name     Product name
  amount   Product price

### transactions

  Field               Description
  ------------------- ---------------------------------
  client_id           Client reference
  gateway_id          Gateway used
  external_id         External gateway transaction ID
  status              Transaction status
  amount              Total amount
  card_last_numbers   Last card digits

### transaction_products

  Field            Description
  ---------------- -----------------------
  transaction_id   Transaction reference
  product_id       Product reference
  quantity         Quantity purchased

------------------------------------------------------------------------

# Running the Project

## Clone Repository

``` bash
git clone https://github.com/Carloshipol/challenge-betalent.git
cd project
```

------------------------------------------------------------------------

## Start Docker Containers

``` bash
docker compose up  -d --build
```

This will start:

-   Laravel API
-   MySQL database
-   Gateway mock services

------------------------------------------------------------------------

## API URL

    http://localhost:8000

------------------------------------------------------------------------

## Gateway Mock Services

Gateway 1

    http://localhost:3001

Gateway 2

    http://localhost:3002

------------------------------------------------------------------------

## Database Seed

Run:

``` bash
php artisan db:seed
```

Example admin user created by `UserSeeder`:

    email: admin@email.com
    password: password
    role: ADMIN

------------------------------------------------------------------------

# Authentication

## Login

**Endpoint**

    POST /api/login

### Request Example

``` json
{
  "email": "admin@email.com",
  "password": "password"
}
```

### Response

``` json
{
  "token": "access-token"
}
```

Use the token in requests:

    Authorization: Bearer TOKEN

------------------------------------------------------------------------

# Purchase Example

## Endpoint

    POST /api/purchase

### Request

``` json
{
  "client": {
    "name": "Carlos",
    "email": "carlos@email.com"
  },
  "products": [
    {
      "product_id": 1,
      "quantity": 1
    }
  ],
  "card_number": "5569000000006063",
  "cvv": "010"
}
```

### Response

``` json
{
  "transaction_id": 1,
  "status": "pending"
}
```

------------------------------------------------------------------------

# Gateway Fallback Logic

### Scenario 1

    cvv: 010

Gateway 1 succeeds.

------------------------------------------------------------------------

### Scenario 2

    cvv: 100

Gateway 1 fails → Gateway 2 succeeds.

------------------------------------------------------------------------

### Scenario 3

    cvv: 200

Both gateways fail → Transaction status becomes **failed**.

------------------------------------------------------------------------

# Queue Processing

Payments are processed **asynchronously**.

Job responsible for payment execution:

    ProcessPayment

------------------------------------------------------------------------

# Running Tests

Execute the test suite:

``` bash
php artisan test
```

Test classes:

-   `GatewayFallbackTest`
-   `PaymentServiceTest`
-   `PurchaseFlowTest`

------------------------------------------------------------------------

# Features Implemented

-   Multi-gateway payment processing
-   Gateway fallback strategy
-   Queue-based processing
-   RESTful API
-   Role-based access control
-   Dockerized environment
-   Automated tests
-   Clean service architecture

------------------------------------------------------------------------

# Future Improvements

-   Gateway discovery using **Strategy Pattern**
-   Retry strategies for gateways
-   Observability and metrics
-   Circuit breaker for unstable gateways

------------------------------------------------------------------------


