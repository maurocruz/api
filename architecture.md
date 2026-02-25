project/
│
├── app/
│   ├── Domain/
│   │		├── Entities/
│   │		│ 	└── Produto.php
│   │		│
│   │		├── Repositories/
│   │		│   └── ProdutoRepositoryInterface.php
│   │		│
│   │		└── Services/
│		│
│   ├── Application/
│   │		├── UseCases/
│   │		│   └── CreateProduto.php
│		│
│   ├── Infrastructure/
│	 	│		├── Persistence/
│   │		│   └── MySqlProdutoRepository.php
│   │		│
│   │		└── Database/
│		│
│   ├── Http/
│   │   ├── Controllers/
│   │   │		└── ProdutoController.php
│   │   ├── Middleware/
│   │   └── routes.php
│   │
│   └── Core/
│
├── public/
│   └── index.php
│
├── bootstrap/
│   └── container.php
│
├── vendor/
└── composer.json

