Установка wkhtmltopdf
=====================

1. Скопировать и распаковать assets/soft/wkhtmltopdf.zip в подходящую директорию.

2. chmod a+x wkhtmltopdf

3. Если при запуске не находит libjpeg8, сделать симлинк с libjpeg62:

     sudo ln -s /usr/lib/libjpeg.so.62 /usr/lib/libjpeg.so.8

4. Установить libXrender:

     sudo apt-get install libxrender1

5. Убедиться, что wkhtmltopdf работает:

     wkhtmltopdf http://google.com google.pdf

6. Настроить путь к wkhtmltopdf в конфиге (в parameters.php).