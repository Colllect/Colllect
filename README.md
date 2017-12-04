Colllect
==========

[![Build Status](https://travis-ci.org/Colllect/Colllect.svg?branch=develop)](https://travis-ci.org/Colllect/Colllect) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/bad0374e-bf29-4ec5-b409-0aa444af152d/mini.png)](https://insight.sensiolabs.com/projects/bad0374e-bf29-4ec5-b409-0aa444af152d) [![MIT Licence](https://img.shields.io/github/license/Colllect/Colllect.svg)](LICENSE)

[Go to Colllect website](http://getcollect.io/) and subscribe to get notified of the launch.


Introduction
------------

Colllect is your new bookmark manager!

With it, you can manage your inspiration and resources into collections.


Project status
--------------

The project is in progress. 

- API is ready with Swagger 2 support :)
- Front still WIP


Install & run
-------------

```bash
git clone git@github.com:Colllect/Colllect.git
cd Colllect/back
composer install
cd ..
cp .env.dist .env
echo "127.0.0.1 colllect.dev" >> /etc/hosts
docker-compose up
```

Go to [http://colllect.dev/api/doc/](http://colllect.dev/api/doc/)
