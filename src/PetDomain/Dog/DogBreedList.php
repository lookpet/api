<?php

declare(strict_types=1);

namespace App\PetDomain\Dog;

final class DogBreedList
{
    public static function getAll(): array
    {
        return [
            'affenpinscher' => 'Аффенпинчер',
            'afghan-hound' => 'Афган (Афганская борзая)',
            'aidi' => 'Аиди (Атласская овчарка)',
            'airedale-terrier' => 'Эрдельтерьер',
            'akita-inu' => 'Акита-ину (Японская акита)',
            'alaskan-malamute' => 'Аляскинский маламут',
            'alpine-dachsbracke' => 'Альпийская таксообразная гончая',
            'american-akita' => 'Большая японская собака (Американская акита)',
            'american-bulldog' => 'Американский бульдог',
            'american-cocker-spaniel' => 'Американский кокер-спаниель',
            'american-foxhound' => 'Американский фоксхаунд',
            'american-staffordshire-terrier' => 'Американский стаффордширский терьер',
            'american-water-spaniel' => 'Американский водяной спаниель',
            'anatolian-shepherd-dog' => 'Анатолийская овчарка',
            'appenzeller-sennenhund-(appenzell-cattle-dog)' => 'Аппенцеллер зенненхунд (Аппенцелльская пастушья собака)',
            'argentine-dogo' => 'Аргентинский дог',
            'ariege-pointer' => 'Ариежский бракк',
            'ariegeois' => 'Арьежуа',
            'artois-hound' => 'Артуазская гончая',
            'australian-cattle-dog' => 'Австралийская пастушья собака',
            'australian-kelpie' => 'Австралийский келпи',
            'australian-shepherd' => 'Австралийская овчарка',
            'australian-silky-terrier' => 'Австралийский шелковистый терьер',
            'australian-terrier' => 'Австралийский терьер',
            'austrian-black-and-tan-hound' => 'Австрийская гончая',
            'austrian-pinscher' => 'Австрийский короткошёрстный пинчер',
            'azawakh' => 'Азавак (Борзая голубых берберов)',
            'barbet' => 'Барбе',
            'basenji' => 'Басенджи',
            'basset-artesien-normand' => 'Артезиано-нормандский бассет',
            'basset-bleu-de-gascogne' => 'Голубой гасконский бассет',
            'beagle' => 'Бигль',
            'beagle-harrier' => 'Бигль харриер',
            'bearded-collie' => 'Бородатая колли',
            'beauceron' => 'Французская гладкошерстная овчарка (Босерон)',
            'bedlington-terrier' => 'Бедлингтон-терьер',
            'bergamasco-shepherd' => 'Бергамаско',
            'berger-blanc-suisse' => 'Белая швейцарская овчарка',
            'berger-picard' => 'Пикардийская овчарка',
            'bernese-mountain-dog' => 'Бернский зенненхунд (Бернская пастушья собака)',
            'bichon-frise' => 'Бишон фризе (Кудрявый бишон)',
            'biewer' => 'Бивер',
            'billy' => 'Бийи',
            'black-and-tan-coonhound' => 'Чёрноподпалый кунхаунд (Енотовая собака)',
            'black-russian-terrier' => 'Русский Чёрный Терьер',
            'bloodhound' => 'Бладхаунд',
            'boerboel' => 'Бурбуль',
            'bolognese' => 'Болоньез',
            'border-collie' => 'Бордер колли',
            'border-terrier' => 'Бордер терьер',
            'borzoi' => 'Русская псовая борзая',
            'bosnian-coarse-haired-hound' => 'Босанский гонич',
            'boston-terrier' => 'Бостон-терьер',
            'bouvier-des-flandres' => 'Фландрский бувье',
            'boxer' => 'Немецкий боксёр',
            'bracco-italiano' => 'Итальянский бракк',
            'braque-d-auvergne' => 'Овернский бракк',
            'braque-du-bourbonnais' => 'Бурбонский бракк',
            'braque-saint-germain' => 'Сен-жерменский бракк',
            'brazilian-terrier' => 'Бразильский терьер',
            'briard' => 'Бриар',
            'briquet-griffon-vendeen' => 'Вандейский гриффон Брике',
            'brittany' => 'Бретонский спаниель',
            'broholmer' => 'Брохолмер',
            'bull-terrier-miniature' => 'Бультерьер Карликовый',
            'bull-terrier-standard' => 'Бультерьер Стандартный ',
            'bullmastiff' => 'Бульмастиф',
            'cairn-terrier' => 'Керн-терьер',
            'canaan-dog' => 'Ханаанская собака',
            'cane-corso' => 'Кане Корсо',
            'cao-da-serra-de-aires' => 'Португальская овчарка',
            'cao-fila-de-sao-miguel' => 'Азорская Пастушья собака (Кау фила де Cан Мигель)',
            'cardigan-welsh-corgi' => 'Вельш корги кардиган',
            'carpathian-shepherd-dog' => 'Восточносибирская лайка',
            'catalan-sheepdog' => 'Каталонская овчарка (Гос д’Атура Катала)',
            'caucasian-shepherd-dog' => 'Кавказская овчарка',
            'cavalier-king-charles-spaniel' => 'Кавалер кинг Чарльз спаниель',
            'central-asian-shepherd-dog' => 'Среднеазиатская овчарка',
            'cesky-fousek' => 'Чешский фоусек',
            'cesky-terrier' => 'Чешский терьер',
            'chesapeake-bay-retriever' => 'Чесапик бэй ретривер',
            'chien-francais-blanc-et-noir' => 'Французская чёрно-пегая гончая',
            'chien-francais-blanc-et-orange' => 'Французская красно-пегая гончая',
            'chien-francais-tricolore' => 'Французская трехцветная гончая',
            'chihuahua' => 'Чихуахуа',
            'chihuahua-long-haired' => 'Чихуахуа Длинношёрстный',
            'chihuahua-smooth-haired' => 'Чихуахуа Гладкошёрстный',
            'chinese-crested-dog' => 'Китайская хохлатая собака',
            'chow-chow' => 'Чау-чау',
            'cimarron-uruguayo' => 'Уругвайский симарон',
            'cirneco-dell-etna' => 'Чирнеко дель Этна (Сицилийская борзая)',
            'clumber-spaniel' => 'Кламбер-спаниель',
            'collie-rough' => 'Колли длинношёрстный',
            'collie-smooth' => 'Колли короткошёрстный',
            'coton-de-tulear' => 'Котон де Туло',
            'croatian-sheepdog' => 'Хорватская овчарка',
            'curly-coated-retriever' => 'Курчавошёрстный ретривер (керли)',
            'czechoslovakian-wolfdog' => 'Чехословацкая волчья собака',
            'dachshund' => 'Такса',
            'dachshund-miniature-long-haired' => 'Такса Карликовая Длинношерстная',
            'dachshund-miniature-smooth-haired' => 'Такса Карликовая Гладкошёрстная',
            'dachshund-miniature-wire-haired' => 'Такса Карликовая Жесткошерстная',
            'dachshund-rabbit-long-haired' => 'Такса Кроличья Длинношерстная',
            'dachshund-rabbit-smooth-haired' => 'Такса Кроличья Гладкошёрстная',
            'dachshund-rabbit-wire-haired' => 'Такса Кроличья Жесткошерстная',
            'dachshund-standard-long-haired' => 'Такса Стандартная Длинношерстная',
            'dachshund-standard-smooth-haired' => 'Такса Стандартная Гладкошёрстная',
            'dachshund-standard-wire-haired' => 'Такса Стандартная Жесткошерстная',
            'dalmatian' => 'Далматин',
            'dandie-dinmont-terrier' => 'Денди-динмонт-терьер',
            'doberman-pinscher' => 'Доберман',
            'dogue-de-bordeaux' => 'Бордоский дог',
            'drentse-patrijshond' => 'Дрентская куропаточная собака',
            'drever' => 'Древер',
            'dunker' => 'Дункер (Норвежская гончая)',
            'dutch-shepherd-dog-long-haired' => 'Голландская овчарка Длинношерстная',
            'dutch-shepherd-dog-rough-haired' => 'Голландская овчарка Жесткошерстная',
            'dutch-shepherd-dog-short-haired' => 'Голландская овчарка Короткошёрстная',
            'dutch-smoushond' => 'Голландский смаусхонд',
            'east-european-shepherd' => 'Восточноевропейская овчарка',
            'english-cocker-spaniel' => 'Английский кокер-спаниель',
            'english-foxhound' => 'Английский фоксхаунд',
            'english-mastiff' => 'Английский мастиф',
            'english-pointer' => 'Пойнтер',
            'english-setter' => 'Английский сеттер',
            'english-springer-spaniel' => 'Английский спрингер-спаниель',
            'english-toy-terrier-(black-&-tan)' => 'Английский той-терьер (чёрноподпалый)',
            'entlebucher-mountain-dog' => 'Энтлебухер зенненхунд (Энтлебухская пастушья собака)',
            'epagneul-bleu-de-picardie' => 'Голубой пикардийский спаниель',
            'estrela-mountain-dog-long-haired' => 'Эштрельская овчарка Длинношерстная',
            'estrela-mountain-dog-smooth-haired' => 'Эштрельская овчарка Гладкошёрстная',
            'eurasier' => 'Евразиер (Ойразиер)',
            'field-spaniel' => 'Филд спаниель',
            'fila-brasileiro' => 'Фила бразилейро',
            'finnish-hound' => 'Финская гончая',
            'finnish-lapphund' => 'Финский лаппхунд',
            'finnish-spitz' => 'Финский шпиц (финская птичья лайка)',
            'flat-coated-retriever' => 'Гладкошёрстный ретривер',
            'formosan-mountain-dog' => 'Тайваньская собака',
            'fox-terrier-smooth-haired' => 'Фокстерьер гладкошёрстный',
            'fox-terrier-wire-haired' => 'Фокстерьер жесткошёрстный',
            'french-bulldog' => 'Французский бульдог',
            'french-pointing-dog---gascogne-type' => 'Французский бракк (гасконский тип)',
            'french-pointing-dog---pyrenean-type' => 'Французский бракк (пиренейский тип)',
            'french-spaniel' => 'Французский спаниель',
            'galgo-espanol' => 'Испанский гальго',
            'german-pinscher' => 'Немецкий пинчер',
            'german-shepherd-dog' => 'Немецкая овчарка',
            'german-shorthaired-pointer' => 'Курцхаар',
            'german-spaniel' => 'Немецкий перепелиный спаниель',
            'german-wirehaired-pointer' => 'Дратхаар',
            'giant-schnauzer' => 'Ризеншнауцер',
            'giant-spitz' => 'Немецкий Шпиц Большой',
            'glen-of-imaal-terrier' => 'Ирландский Глен оф Имаал терьер',
            'golden-retriever' => 'Золотистый ретривер',
            'gordon-setter' => 'Шотландский сеттер (гордон)',
            'grand-anglo-francais-blanc-et-noir' => 'Большая англо-французская чёрно-пегая гончая',
            'grand-anglo-francais-blanc-et-orange' => 'Большая англо-французская красно-пегая гончая',
            'grand-anglo-francais-tricolore' => 'Большая англо-французская трехцветная гончая',
            'grand-bleu-de-gascogne' => 'Большой гасконский сентонжуа',
            'grand-griffon-vendeen' => 'Большой вандейский гриффон',
            'great-dane' => 'Немецкий дог',
            'great-pyrenees' => 'Пиренейская горная собака',
            'greater-swiss-mountain-dog' => 'Большой швейцарский зенненхунд (Большая швейцарская пастушья собака)',
            'greenland-dog' => 'Гренландская собака',
            'greyhound' => 'Грейхаунд',
            'griffon' => 'Гриффон',
            'griffon-belge-(belgian-griffon)' => 'Бельгийский гриффон',
            'griffon-bleu-de-gascogne' => 'Голубой гасконский гриффон',
            'griffon-bruxellois-(brussels-griffon)' => 'Брюссельский гриффон',
            'griffon-fauve-de-bretagne' => 'Бретонский рыжеватый гриффон',
            'griffon-nivernais' => 'Нивернесский гриффон',
            'groenendael-(belgian-shepherd-dog)' => 'Грюнендаль (Бельгийская овчарка)',
            'hamiltonstovare' => 'Гончая Гамильтона',
            'hanover-hound' => 'Ганноверская гончая',
            'harlequin' => 'Арлекин',
            'harrier' => 'Харьер',
            'havanese' => 'Гаванский бишон',
            'hokkaido' => 'Хоккайдо',
            'hovawart' => 'Ховаварт',
            'hungarian-hound' => 'Трансильванская гончая (Эрдели копо)',
            'ibizan-hound-rough-haired' => 'Поденко ибисенко Жесткошерстный',
            'ibizan-hound-smooth-haired' => 'Поденко ибисенко Гладкошёрстный',
            'icelandic-sheepdog' => 'Исландская собака (Исландская сторожевая)',
            'irish-red-and-white-setter' => 'Ирландский красно-белый сеттер',
            'irish-setter' => 'Ирландский красный сеттер',
            'irish-terrier' => 'Ирландский терьер',
            'irish-water-spaniel' => 'Ирландский водяной спаниель',
            'irish-wolfhound' => 'Ирландский волкодав',
            'istrian-coarse-haired-hound' => 'Истарский грубошёрстный гонич',
            'istrian-shorthaired-hound' => 'Истарский мягкошёрстный гонич',
            'italian-greyhound' => 'Левретка',
            'italian-hound-coarse-haired' => 'Итальянская гончая — длинношёрстная',
            'italian-hound-short-haired' => 'Итальянская гончая — короткошёрстная',
            'jack-russell-terrier' => 'Джек-Рассел терьер',
            'jagdterrier' => 'Немецкий ягдтерьер',
            'jamthund' => 'Ямтхунд',
            'japanese-chin' => 'Японский хин',
            'japanese-spitz' => 'Японский шпиц',
            'japanese-terrier' => 'Японский терьер',
            'kai-ken' => 'Кай (тора-кен)',
            'karelian-bear-dog' => 'Карельская медвежья собака',
            'karst-shepherd' => 'Карстская овчарка',
            'kerry-blue-terrier' => 'Керри-блю-терьер',
            'king-charles-spaniel' => 'Кинг чарльз спаниель',
            'kishu' => 'Кисю (Кишу)',
            'komondor' => 'Комондор',
            'kooikerhondje' => 'Кокерхонде',
            'korean-jindo-dog' => 'Корейский джиндо',
            'kromfohrlander' => 'Кромфорлендер',
            'kuvasz' => 'Кувас',
            'labrador-retriever' => 'Лабрадор ретривер',
            'laekenois-(belgian-shepherd-dog)' => 'Лакенуа (Бельгийская овчарка)',
            'lagotto-romagnolo' => 'Романская водяная собака',
            'lakeland-terrier' => 'Лейклэнд терьер',
            'landseer' => 'Ландсир — европейский континентальный тип',
            'lapponian-herder' => 'Лапинпорокойра (Лапландская оленегонная собака)',
            'large-munsterlander' => 'Большая мюнстерлендерская легавая',
            'leonberger' => 'Леонбергер',
            'lhasa-apso' => 'Лхаса апсо (Лхасский апсо)',
            'lowchen' => 'Малая львиная собака (Лёвхен)',
            'majorca-shepherd-dog' => 'Ка де Бестиар',
            'malinois-(belgian-shepherd-dog)' => 'Малинуа (Бельгийская овчарка)',
            'maltese' => 'Мальтийская болонка (Мальтезе)',
            'manchester-terrier' => 'Манчестер терьер',
            'maremma-sheepdog' => 'Мареммано Абруццеле',
            'medium-size-spitz' => 'Немецкий Шпиц Средний',
            'mexican-hairless-dog-intermediate' => 'Мексиканская Голая Собака Средняя',
            'mexican-hairless-dog-miniature' => 'Мексиканская Голая Собака Миниатюрная',
            'mexican-hairless-dog-standard' => 'Мексиканская Голая Собака Стандартная',
            'miniature-pinscher' => 'Карликовый пинчер',
            'miniature-schnauzer' => 'Цвергшнауцер',
            'miniature-spitz' => 'Немецкий Шпиц Малый',
            'mioritic' => 'Румынская миоритская овчарка',
            'montenegrin-mountain-hound' => 'Черногорская гончая',
            'moscow-watchdog' => 'Московская сторожевая',
            'mudi' => 'Муди',
            'neapolitan-mastiff' => 'Мастино неаполетано',
            'newfoundland' => 'Ньюфаундленд',
            'norfolk-terrier' => 'Норфолк терьер',
            'norrbottenspets' => 'Норботтен шпиц',
            'norwegian-buhund' => 'Норвежский бухунд',
            'norwegian-elkhound' => 'Норск элгхунд гра (серый) (серая норвежская лосиная лайка)',
            'norwegian-elkhound-gray' => 'Норск элгхунд сорт (чёрный) (чёрная норвежская лосиная лайка)',
            'norwegian-elkhound-gray' => 'Норск лундехунд',
            'norwegian-lundehund' => 'Хигенхунд',
            'norwich-terrier' => 'Норвич терьер',
            'nova-scotia-duck-tolling-retriever' => 'Новошотландский ретривер',
            'old-danish-pointer' => 'Датская легавая',
            'old-english-sheepdog' => 'Староанглийская овчарка (Бобтейл)',
            'otterhound' => 'Оттерхаунд (Выдровая собака)',
            'papillon' => 'Папийон',
            'parson-russell-terrier' => 'Парсон Рассел терьер',
            'pekingese' => 'Пекинес',
            'pembroke-welsh-corgi' => 'Вельш корги пемброк',
            'perro-de-presa-canario' => 'Канарский дог',
            'perro-de-presa-mallorquin' => 'Ка-де-бо (Перро дого Майоркин)',
            'peruvian-hairless-dog-large' => 'Перуанская голая собака Большая',
            'peruvian-hairless-dog-medium-sized' => 'Перуанская голая собака Средняя',
            'peruvian-hairless-dog-miniature' => 'Перуанская голая собака Миниатюрная',
            'petit-basset-griffon-vendeen' => 'Малый вандейский бассет-гриффон',
            'petit-bleu-de-gascogne' => 'Малая голубая гасконская гончая',
            'petit-brabanson' => 'Пти брабансон',
            'phalene' => 'Фален',
            'phantom' => 'Фантом',
            'pharaoh-hound' => 'Фарао хаунд (Фараонова собака)',
            'picardy-spaniel' => 'Пикардийский спаниель',
            'pitbull' => 'Американский питбультерьер',
            'podenco-canario' => 'Поденко канарио',
            'polish-greyhound' => 'Польский харт',
            'polish-hound' => 'Польский огар (Польская гончая)',
            'polish-hunting-dog' => 'Польская гончая (Польская охотничья собака)',
            'polish-lowland-sheepdog' => 'Польская низинная овчарка',
            'polish-tatra-sheepdog' => 'Польская подгалянская овчарка',
            'pomeranian' => 'Померанский (Карликовый Шпиц)',
            'pont-audemer-spaniel' => 'Понт-одемерский спаниель',
            'poodle-medium' => 'Пудель Малый',
            'poodle-miniature' => 'Пудель Карликовый',
            'poodle-standard' => 'Пудель Большой',
            'poodle-toy' => 'Пудель Той',
            'porcelaine' => 'Порселейн',
            'portuguese-podengo-large-smooth-haired' => 'Португальский поденго Большой Гладкошёрстный',
            'portuguese-podengo-large-wire-haired' => 'Португальский поденго Большой Жесткошерстный',
            'portuguese-podengo-medium-sized-smooth-haired' => 'Португальский поденго Средний Гладкошёрстный',
            'portuguese-podengo-medium-sized-wire-haired' => 'Португальский поденго Средний Жесткошерстный',
            'portuguese-podengo-miniature-smooth-haired' => 'Португальский поденго Миниатюрный Гладкошёрстный',
            'portuguese-podengo-miniature-wire-haired' => 'Португальский поденго Миниатюрный Жесткошерстный',
            'portuguese-pointer' => 'Португальская легавая',
            'portuguese-water-dog' => 'Португальская водяная собака',
            'pudelpointer' => 'Пудель-поинтер',
            'pug' => 'Мопс',
            'puli' => 'Пули (венгерская водяная собака)',
            'pumi' => 'Пуми',
            'pyrenean-mastiff' => 'Пиренейский мастиф',
            'pyrenean-shepherd' => 'Пиренейская длинношёрстная овчарка',
            'rafeiro-do-alentejo' => 'Рафейро до Алентехо (Португальская сторожевая)',
            'rhodesian-ridgeback' => 'Родезийский риджбек',
            'rottweiler' => 'Ротвейлер',
            'russian-spaniel' => 'Русский охотничий спаниель',
            'russian-toy' => 'Русский той',
            'russian-tsvetnaya-bolonka' => 'Русская цветная болонка',
            'russian-european-laika' => 'Русско-европейская лайка',
            'saarlooswolfhond' => 'Волчья собака Сарлоса',
            'sabueso-espanol' => 'Испанская гончая',
            'saluki' => 'Салюки',
            'samoyed' => 'Самоедская собака (самоед)',
            'sarplaninac' => 'Шарпланинак',
            'schapendoes' => 'Шапендуа',
            'schillerstovare' => 'Гончая Шиллера',
            'schipperke' => 'Шипперке',
            'schweizer-laufhund-bernese-hound' => 'Швейцарская гончая Бернская',
            'schweizer-laufhund-jura-hound' => 'Швейцарская гончая Юрская',
            'schweizer-laufhund-lucerne-hound' => 'Швейцарская гончая Люцернская',
            'schweizer-laufhund-schwyz-hound' => 'Швейцарская гончая Швицская',
            'scottish-deerhound' => 'Дирхаунд',
            'scottish-terrier' => 'Скотч-терьер (шотландский терьер)',
            'sealyham-terrier' => 'Силихем терьер',
            'serbian-hound' => 'Сербская гончая',
            'serbian-tricolour-hound' => 'Сербская трехцветная гончая',
            'shar-pei' => 'Шарпей',
            'shetland-sheepdog' => 'Шетландская овчарка (Шелти)',
            'shiba-inu' => 'Сиба-ину (малая японская собака)',
            'shih-tzu' => 'Ши-тцу',
            'shikoku' => 'Сикоку (Шикоку)',
            'siberian-husky' => 'Сибирский хаски',
            'skye-terrier' => 'Скай терьер',
            'sloughi' => 'Слюги (Арабская борзая)',
            'slovak-cuvac' => 'Словацкий Чувач',
            'slovakian-rough-haired-pointer' => 'Словацкая жесткошёрстная легавая',
            'slovensky-kopov' => 'Словацкая гончая (Словацкий копов)',
            'small-munsterlander' => 'Малая мюнстерлендерская легавая',
            'small-swiss-bernese-hound' => 'Швейцарская низкорослая гончая Бернская',
            'small-swiss-jura-hound' => 'Швейцарская низкорослая гончая Юрская',
            'small-swiss-lucerne-hound' => 'Швейцарская низкорослая гончая Люцернская',
            'small-swiss-schwyz-hound' => 'Швейцарская низкорослая гончая Швицская',
            'soft-coated-wheaten-terrier' => 'Ирландский мягкошёрстный пшеничный терьер',
            'south-russian-ovcharka' => 'Южнорусская овчарка',
            'spanish-mastiff' => 'Испанский мастиф',
            'spanish-water-dog' => 'Испанская водная собака',
            'spinone-italiano' => 'Итальянский спиноне',
            'st-bernar' => 'Сенбернар',
            'st-bernard-long-haired' => 'Сенбернар Длинношёрстный',
            'st-bernard-short-haired' => 'Сенбернар Короткошёрстный',
            'stabyhoun' => 'Стабихун',
            'staffordshire-bull-terrier' => 'Стаффордширский бультерьер',
            'standard-schnauzer' => 'Миттельшнауцер',
            'styrian-coarse-haired-hound' => 'Штирская гончая',
            'sussex-spaniel' => 'Сассекс спаниель',
            'swedish-lapphund' => 'Шведский лаппхунд',
            'swedish-vallhund' => 'Шведский вальхунд (Вестготашпиц)',
            'tervueren-(belgian-shepherd-dog)' => 'Тервюрен (Бельгийская овчарка)',
            'thai-ridgeback' => 'Тайский риджбек',
            'tibetan-mastiff' => 'Тибетский мастиф (Тибетский дог или До-хи)',
            'tibetan-spaniel' => 'Тибетский спаниель',
            'tibetan-terrier' => 'Тибетский терьер',
            'tornjak' => 'Торньяк (Боснийско-Герцоговинско-Хорватская пастушеская собака)',
            'tosa' => 'Тоса-ину',
            'tuvinskaya-ovcharka' => 'Тувинская овчарка',
            'tyrolean-hound' => 'Тирольская гончая',
            'vizsla' => 'Венгерская короткошёрстная выжла',
            'volpino-italiano' => 'Вольпино итальяно',
            'weimaraner-long-haired' => 'Веймаранер Длинношёрстный',
            'weimaraner-short-haired' => 'Веймаранер Короткошёрстный',
            'welsh-springer-spaniel' => 'Вельш-спрингер-спаниель',
            'welsh-terrier' => 'Вельш-терьер',
            'west-highland-white-terrier' => 'Вест хайленд уайт терьер',
            'west-siberian-laika' => 'Западносибирская лайка',
            'westphalian-dachsbracke' => 'Вестфальская таксообразная гончая',
            'wetterhoun' => 'Веттерхун (Голландский водяной спаниель)',
            'whippet' => 'Уиппет',
            'wirehaired-pointing-griffon' => 'Гриффон Кортальса',
            'wirehaired-vizsla' => 'Венгерская жесткошёрстная выжла',
            'wolfsspitz-(keeshond)' => 'Вольф-Шпиц (Кесхонд)',
            'yorkshire-terrier' => 'Йоркширский терьер',
        ];
    }
}
