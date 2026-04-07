
drop schema if exists `Reverie`;

create schema if not exists `Reverie`;
use `Reverie`;

-- employee tables, includes the administrator --
create table employees(
	employee_id int auto_increment primary key,
    e_name varchar(50) not null,
    e_surname varchar(50) not null,
    e_username varchar(20) not null,
    e_passwordHash varchar(255) not null,
    e_role enum('administrator', 'employee') not null default 'employee',
    e_shift varchar(30) default 'Monday, Friday',
    e_todaySales decimal(10,2) not null default 0,
    e_totalSales decimal(10,2) default 0,
    e_profilePhoto varchar(255),
    e_phone varchar(15),
    last_login date,
    creation_date datetime not null default current_timestamp,
    modification_date datetime on update current_timestamp,
    state enum('alive', 'deactivated') default 'alive',
    unique index `idx_username` (e_username)
);
-- -------------------------------------------------------------------------------------------------- --

-- example insertions, that's not how the password hash is gonna be handled, but works for now--
insert into employees(`e_name`, `e_surname`, `e_username`, `e_passwordHash`, `e_phone`, `e_shift`)
values
	('Pinkie', 'Pie', 'pinkie123', 'strongpasswordabc', '7711909723', 'Monday, Friday'),
    ('Amy', 'Rose', 'amyrose65', '__my__password__123', '7712243798', 'Tuesday, Saturday'),
    ('Hello', 'Kitty', 'littlekitty', 'unhackablepwd', '7710987321', 'Wednesday, Sunday'),
    ('Patrick', 'Star', 'ispatrick', 'no, this is patrick', '9999999999', 'Thursday'),
    ('Jenny', 'XJ9', 'teenRobot', 'Life stinks man', 'employee', 'Monday, Thursday')
;
insert into employees(`e_name`, `e_surname`, `e_username`, `e_passwordHash`, `e_role`)
values('Violet', 'Evergarden', 'VioletE', '1707##love', 'administrator');

select * from employees;

/*LISTA DE PASSWORDS
USERNAME / PWD
pinkiepierules / strongpasswordabc
amyrose65 / __my__password__123
littlekitty / unhackablepwd
ispatrick / no, this is patrick
teenRobot / Life stinks man

ADMIN
VioletE / 1707owolove
*/
-- -------------------------------------------------------------------------------------------------- --

-- Categories, like types of products grouped togheter --
create table categories (
    category_id int auto_increment primary key,
    category_name varchar(50) not null unique
);

-- product table --
create table products(
	product_code varchar(10) not null primary key,
    p_name varchar(50) not null,
    p_description varchar(255) not null,
    p_price decimal(10,2) not null default 0,
    p_amount int unsigned not null default 0,
    p_timesSold int unsigned not null default 0,
    p_state enum('available', 'soldout', 'discontinued') default 'soldout',
    p_photo varchar(255),
    category_id int not null,
    creation_date datetime not null default current_timestamp,
    modification_date datetime on update current_timestamp,
    constraint `product_category` foreign key (category_id) references categories(`category_id`),
    unique index `idx_name` (p_name)
);
-- -------------------------------------------------------------------------------------------------- --

-- --------------------------- example insertions --------------------------------------------------- --
insert into categories(`category_name`)
values
('White bread'),
('Sweet Bread'),
('Cakes');

insert into products(`product_code`, `p_name`, `p_description`, `p_price`, `category_id`, `p_amount`)
values
('dnch1', 'Chocolate Doughnut', 'Medium-size Chocolate Doughnut', 12, 2, 40),
('whbr1', 'Bolillo', 'Mexican white salty bread ideal for a sandwich', 3, 1, 50),
('whbr2', 'Bagette', 'Classic french style long white and salty bread', 8, 1, 60);

select * from products;
-- -------------------------------------------------------------------------------------------------- --

-- sales table, records the time of the sale and which employee performed it------------------------- --
create table sales(
	sale_id int not null auto_increment primary key,
    s_date date not null,
    s_amount decimal(10,2) not null default 0,
    s_saler int not null,
    creation_date datetime not null default current_timestamp,
    modification_date datetime on update current_timestamp,
    constraint `employee_did_sale` foreign key (`s_saler`) references employees(`employee_id`)
);
-- -------------------------------------------------------------------------------------------------- --

-- because one sale can include many products, and one product can be bought many times ------------- --
create table sale_sold_products(
	sale_id int not null,
    product_code varchar(10) not null,
    p_amountSold int not null,
    creation_date datetime not null default current_timestamp,
    modification_date datetime on update current_timestamp,
    constraint `sale_done` foreign key (`sale_id`) references sales(`sale_id`),
    constraint `product_sold` foreign key (`product_code`) references products(`product_code`),
    primary key (`sale_id`, `product_code`)
);
-- -------------------------------------------------------------------------------------------------- --

-- table to handle sales reports --
create table daily_report(
	dailyReport_id int not null auto_increment primary key,
    dr_date date not null,
    dr_moneyGained decimal(10,2) not null,
    dr_mostSoldProduct varchar (10) not null,
    dr_totalSales int unsigned not null default 0,
    creation_date datetime not null default current_timestamp,
    modification_date datetime on update current_timestamp,
    constraint `p_mostSold` foreign key(`dr_mostSoldProduct`) references products(product_code)
);
-- -------------------------------------------------------------------------------------------------- --

-- Views used for some selection queries ---------------------------------------------------------------
create view vwEmployee as 
select employee_id as ID, concat(e_name, ' ', e_surname) as `Name`, e_totalSales as 'Total Sales', e_todaySales as 'Today Sales',
e_profilePhoto as Photo, e_role as `Role`, e_shift as Shift
from employees;

create view vwProduct as
select product_code as `Code`, p_name as Product, concat('$', p_price) as Price, p_description as `Description`, p_amount as Amount,
category_name as `Type`, p_photo as Photo, p_state as State
from products join categories on products.category_id = categories.category_id;

select * from vwEmployee;
select * from vwProduct;
-- -------------------------------------------------------------------------------------------------- --

/* 
============================================================================================================
============================================================================================================
Code made by Francisco Emmanuel Luna Hidalgo Last checked 25/04/2026 
============================================================================================================
============================================================================================================
Instituto Tecnológico de Pachuca, Ingeniería en Sistemas Computacionales, Programación Web, proyecto final
============================================================================================================
============================================================================================================
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%%%%%%%%%%##%%%%%%%%%%@@@@@@@@@@@@@@@@@@@@@@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%%%#*++++++++++++++++++++++++++++*#%%%%%%@@@@@@@@@@@@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%#*+++++++++++++++++++++++++++++++++++++++++++*##%%%@@@@@@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%+++++++++++++++++++++++++++++++++++++++++++++++++++++*#%%@@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@%%@@@@@#+++++++++++++++++++++++++++++++++++++++++++++++++++++++%@@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@%%#+#%@@@@%*++++##+++++++++++++++++++++++++++++++++++++++++++++++%%@@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@%%*+++++%%@@@@%*+++%@@@%#*+++++++++++++++++++++++++++++++++++++++++#%@@@@@@@@
    @@@@@@@@@@@@@@@@@@@@@%#++++++++*%@@@@@%*++%@@@@@@@%#+++++++++++++++++++++++++++++++++++++*%@@@@@@@@@
    @@@@@@@@@@@@@@@@@@@%#++++++++++=#%@@@@@@#+%@@@@@@@@@@%#++++++++++++++++++++++++++++++++++%@@@@@@@@@@
    @@@@@@@@@@@@@@@@@%#++++++++++++++%@@@@@@@%%@@@@@@@@@@@@%%*++++++++++++++++++++++++++++++#%@@@@@@@@@@
    @@@@@@@@@@@@@@@%#++++++++++++++++*%@@@@@@@@@@@@@@@@@@@@@@@%#*++++++++++++++++++++++++++*%@@@@@@@@@@@
    @@@@@@@@@@@@@%%*++++++++++++++++++#%@@@@@@@@@@@@@@@@@@@@@@@@@%#+++++++++++++++++++++++*%@@@@@@@@@@@@
    @@@@@@@@@@@@%#+++++++++++++++++++++%%@@@@@@@@@@@@@@@@@@@@@@@@@@%%*++++++++++++++++++++#%@@@@@@@@@@@@
    @@@@@@@@@@@%*+++++++++++++++++++++++%@@@@@@@@@@@@@@@@@@@@@@@@@@@@%%#+++++++++++++++++#%@@@@@@@@@@@@@
    @@@@@@@@@@%+++++++++++++++++++++++++*%@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%#++++++++++++++*%@@@@@@@@@@@@@@
    @@@@@@@@%#+++++++++++++++++++++++++++#%@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%#++++++++++++%@@@@@@@@@@@@@@@
    @@@@@@@%%+++++++++++++++++++++++++++++%@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%#*++++++++#%@@@@@@@@@@@@@@@
    @@@@@@%%++++++++++++++++++++++++++++++*%@@@@@@@@@@@@@@%%%%%%%%%%%%%%%%@@@@%%##+--*%%@@@@@@@@@@@@@@@@
    @@@@@@%+++++++++++++++++++++++++++++++#%++*#%@@@@%%##*++++++++++++++++*#%%%%=...-=.=%@@@@@@@@@@@@@@@
    @@@@@%*+++++++++++++++++++++++++++++**:-+...-#%#*+++++++++++++++++++++++++##...:*...#@@@@@@@@@@@@@@@
    @@@@%*++++++++++++++++++++++++++++++#-..:+...=%+++++++++++++++++++++++++++*%:..*...:%@@@@@@@@@@@@@@@
    @@@%#+++++++++++++++++++++++++++++++#=...-=..+#++++++++++++++++++++++++++++#%++-..+%@@@@@@@@@@@@@@@@
    @@@%+++++++++++++++++++++++++++++**#%%+:..-**#+++++++++++++++++++++++++++++++*####**#%@@@@@@@@@@@@@@
    @@%#+++++++++++++++++++++++++*#%%@@@%#*#%#%#++++++++++++++++++++++++++++++++++++++++++#%@@@@@@@@@@@@
    @@%++++++++++++++++++++++*#%%@@@@@@%++++++++++++++++++++++++++++++++++++++=+===========*%@@@@@@@@@@@
    @%#+++++++++++++++++++*%%@@@@@@@@%+-=++++++++++++++++++++++++++++++++++++++=:...........:#@@@@@@@@@@
    @%*+++++++++++++++*#%@@@@@@@@@@@%+....-=++++++++++++++++++++=--==++++++++++++=-..........:*%@@@@@@@@
    @%++++++++++++++#%@@@@@@@@@@@@@%+........:=+++++++++++++++++++=.....:-==++++++++=..........#%@@@@@@@
    %#+++++++++++*%@@@@@@@@@@@@@@@%*.............:-===++++++++++++++-.................:-++=:....%@@@@@@@
    %#+++++++++#@@@@@@@@@@@@@@@@@@#:............:-::...::--===+++++++=-....................-*:..-%@@@@@@
    %#+++++=*%@@@@@@@@@@@@@@@@@@@%=..  ......:*=....................................+%@@%+...-:..+@@@@@@
    %#++++++++****#%@@@@@@@@@@@@@#:.     ....+.....:=*#*=:....  .... .....      ..+@@@#.:#@-.....-%@@@@@
    %*+++++++++*#%@@@@@@@@@@@@@@%+.. .   ...::...=@@@@=:-+%*:.                  .*@@@@@+..*@:....:#@@@@@
    %*=+++*##%%@@@@@@@@@@@@@@@@@%=..      ......#@@@@@#....-%+...   .        ...+@@@@@@%..:@#.....*%@@@@
    %%%%%@@@@@@@@@@@@@@@@@@@@@@@%=..      .....#@@@@@@@:.....#*..            ..-@@@@@*:*...*%.....+%@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@%=..         .-@@@@@@%*=.....:#*.           ...%@#=.:=#*...=@:. ..+%@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@%=...        .*@@@#-.:*=......:@+...         .++.:*@@@@-...-@:. ..+%@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@%+...  .     .#%:.:#@@@=...  ..+@:..         .#@@@@@@@%....=@:....+%@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@*..         :#+#@@@@@@:...  ...%*..        .-%@@@@@@@=.  .+%.....*@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@#-..        :#@@@@@@@#....  ...=#:.       ..=@@@@@@@#.. ..*+....:#@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@+.         .#@@@@@@@=.     ....%-.      ...+@@@@@@%..  ..%:....-%@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%:.......  .*@@@@@@#:.     ....*=.      ...*@@@@@%......-*.....*@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@#.......  .+@@@@@@:..      . .==. .     ..*@@@@+... ...+:....-%@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%#......  .:@@@@@....   .    .-=.     . ..#@@+........:=.....%%@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%*:..... ..#@%+.....       ..:=.       ..=:..:::::::-=:....==--#%@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@#:.......-+::---===++==+++++-..........:--:::....... ......:*%@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%=............................ ...................   ....-%@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%*:......     ..-*+-:....................     .   ....:#%@@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%*:.......  ...:+-:=+*#%%%###***++++..............:+%@@@@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%*:............=#-............:*-.............:*%@@@@@@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%%=............=#*:......:+#-.............-#%@@@@@@@@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%#=:...........=+****+-............:=#%@@@@@@@@@@@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%#+-:......................-+#%@@@@@@@@@@@@@@@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%%%%#*+=-::::::-=+#%%%%@@@@@@@@@@@@@@@@@@@@@@@@
    @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@%+**##%%%@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
============================================================================================================
============================================================================================================
*/