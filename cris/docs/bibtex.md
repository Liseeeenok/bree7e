
## Типы записей
* article  
Статья из журнала.  
Необходимые поля: AUTHOR, TITLE, JOURNAL, YEAR  
Дополнительные поля: VOLUME, NUMBER, PAGES, MONTH, NOTE, KEY

* book  
Определённое издание книги.  
Необходимые поля: AUTHOR/EDITOR, TITLE, PUBLISHER, YEAR  
Дополнительные поля: VOLUME, SERIES, ADDRESS, EDITION, MONTH, NOTE, KEY, PAGES

* inbook  
Часть книги, возможно без названия. Может быть главой (частью, параграфом), либо диапазоном
страниц.  
Небходимые поля: AUTHOR/EDITOR, TITLE, CHAPTER/PAGES, PUBLISHER, YEAR  
Дополнительные поля: VOLUME, SERIES, ADDRESS, EDITION, MONTH, NOTE, KEY

* inproceedings  
Тезис (труд) конференции.  
Небходимые поля: AUTHOR, TITLE, BOOKTITLE, YEAR  
Дополнительные поля: EDITOR, SERIES, PAGES, ORGANIZATION, PUBLISHER, ADDRESS, MONTH, NOTE, KEY

* phdthesis  
Кандидатская диссертация.  
Небходимые поля: AUTHOR, TITLE, SCHOOL, YEAR  
Дополнительные поля: ADDRESS, MONTH, NOTE, KEY

## Поля записей
* address: Адрес издателя (обычно просто город, но может быть полным адресом для
малоизвестных издателей)
* annote: Аннотация для библиографической записи.
* author: Имена авторов (если больше одного, то разделяются and)
* booktitle: Наименование книги, содержащей данную работу.
* chapter: Номер главы
* crossref: Ключ кросс-ссылки (позволяет использовать другую библио-запись в качестве
названия, например, сборника трудов)
* edition: Издание (полная строка, например, «1-е, стереотипное»)
* editor: Имена редакторов (оформление аналогично авторам)
* eprint: A specification of an electronic publication, often a preprint or a technical report
* howpublished: Способ публикации, если нестандартный
* institution: Институт, вовлечённый в публикацию, необязательно издатель
* journal: Название журнала, содержащего статью
* key: Скрытое ключевое поле, задающее порядок сортировки (если «author» и «editor» не заданы).
* month: Месяц публикации (может содержать дату). Если неопубликовано — создания.
* note: Любые заметки
* number: Номер журнала
* organization: Организатор конференции
* pages: Номера страниц, разделённые запятыми или двойным дефисом. Для книги — общее количество страниц.
* publisher: Издатель
* school: Институт, в котором защищалась диссертация.
* series: Серия, в которой вышла книга.
* title: Название работы
* type: Тип отчёта, например «Заметки исследователя»
* url: WWW-адрес