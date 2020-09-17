# m work sample

A web service for quiz questions.

### Prerequisites
* sqlite3
* PHP ^7.3

### Installation
* Clone the repository with `git clone git@github.com:oelek/m-work-sample.git`
* Change working dir `cd m-work-sample`
* Install dependencies `composer install`
* Copy .env.example with `composer run post-root-package-install`
* Generate key with `php artisan key:generate`
* Migrate and seed the database with `php artisan migrate --seed`
* Start server `php artisan serve`

### Auth
All requests must have a basic authorization header present. 
A user should be seeded with credentials: `Authorization : Basic Z2FtZUBtZXIuY29tOnBhc3N3b3Jk`

### Routes
|Verb    |  URI                                     | Description       | Example payload        |
|--------|------------------------------------------|-------------------|------------------------|
| POST   |  /api/games                              | Create new game   | {"quiz_id": 1}         |
| GET    |  /api/games/{game}/questions/{question}  | Show question     |                        |
| POST   |  /api/games/{game}/questions/{question}  | Submit answer     | {"answer": "A string"} |
| GET    |  /api/games/{game}                       | Show results      |                        |

#### Lifelines
There is two life lines available. '5050', which removes two incorrect options, and 'buytime' which increases the time limit with 10s. The example below show the question with only two options.

```
/api/games/{game}/questions/{question}?lifeline=5050
```

### Tests
Run tests with `composer run test`
