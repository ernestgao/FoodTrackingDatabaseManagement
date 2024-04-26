
BEGIN
EXECUTE IMMEDIATE 'DROP TABLE COUNTRY CASCADE CONSTRAINTS';
EXECUTE IMMEDIATE 'DROP TABLE FOODSERVICEFROM CASCADE CONSTRAINTS';
EXECUTE IMMEDIATE 'DROP TABLE FOODPRODUCTSELL CASCADE CONSTRAINTS';
EXECUTE IMMEDIATE 'DROP TABLE GROCERYSTORE CASCADE CONSTRAINTS';
EXECUTE IMMEDIATE 'DROP TABLE RESTAURANT CASCADE CONSTRAINTS';
EXECUTE IMMEDIATE 'DROP TABLE CONTAININGREDIENT CASCADE CONSTRAINTS';
EXECUTE IMMEDIATE 'DROP TABLE PURCHASE CASCADE CONSTRAINTS';
EXECUTE IMMEDIATE 'DROP TABLE NUTRITIONBENEFITS CASCADE CONSTRAINTS';
EXECUTE IMMEDIATE 'DROP TABLE INCLUDENUTRITION CASCADE CONSTRAINTS';
EXECUTE IMMEDIATE 'DROP TABLE INGREDIENTEXPIRATION CASCADE CONSTRAINTS';
EXECUTE IMMEDIATE 'DROP TABLE DISEASESOLUTIONS CASCADE CONSTRAINTS';
EXECUTE IMMEDIATE 'DROP TABLE AGRICULTURALDISEASE CASCADE CONSTRAINTS';
EXECUTE IMMEDIATE 'DROP TABLE AFFECT CASCADE CONSTRAINTS';
EXECUTE IMMEDIATE 'DROP TABLE SYMPTOMEFFECTS CASCADE CONSTRAINTS';
EXECUTE IMMEDIATE 'DROP TABLE HEALTHISSUE CASCADE CONSTRAINTS';
EXECUTE IMMEDIATE 'DROP TABLE MEDICINE CASCADE CONSTRAINTS';
EXECUTE IMMEDIATE 'DROP TABLE CAUSE CASCADE CONSTRAINTS';
EXECUTE IMMEDIATE 'DROP TABLE TREATMENT CASCADE CONSTRAINTS';

EXECUTE IMMEDIATE 'CREATE TABLE COUNTRY (
    COUNTRYNAME CHAR(20) PRIMARY KEY,
    POPULATION REAL NOT NULL,
    CONTINENT CHAR(20) NOT NULL
)';

EXECUTE IMMEDIATE 'CREATE TABLE FOODSERVICEFROM (
    FSID INT PRIMARY KEY,
    FSNAME CHAR(20),
    FSADDRESS VARCHAR(150),
    HOURS VARCHAR(150),
    COUNTRYNAME CHAR(20) NOT NULL,
    FOREIGN KEY (COUNTRYNAME) REFERENCES COUNTRY(COUNTRYNAME),
    UNIQUE(FSNAME,FSADDRESS)
)';

EXECUTE IMMEDIATE 'CREATE TABLE FOODPRODUCTSELL (
    FPNAME CHAR(20) PRIMARY KEY,
    DESCRIPTION VARCHAR(150),
    RECIPE VARCHAR(150),
    FSID INT,
    FOREIGN KEY (FSID) REFERENCES FOODSERVICEFROM(FSID)
)';

EXECUTE IMMEDIATE 'CREATE TABLE GROCERYSTORE (
    FSID INT PRIMARY KEY,
    GSLICENSE INT UNIQUE,
    DEPARTMENTS INT,
    FOREIGN KEY (FSID) REFERENCES FOODSERVICEFROM(FSID)
)';

EXECUTE IMMEDIATE 'CREATE TABLE RESTAURANT (
    FSID INT PRIMARY KEY,
    RLICENSE INT UNIQUE,
    TABLES INT,
    FOREIGN KEY (FSID) REFERENCES FOODSERVICEFROM(FSID)
)';

EXECUTE IMMEDIATE 'CREATE TABLE CONTAININGREDIENT (
    PRODUCEID INT PRIMARY KEY,
    INAME CHAR(20),
    ORIGIN CHAR(20),
    MFGDATE DATE,
    FPNAME CHAR(20),
    FOREIGN KEY (FPNAME) REFERENCES FOODPRODUCTSELL(FPNAME),
    UNIQUE(INAME, ORIGIN, MFGDATE)
)';

EXECUTE IMMEDIATE 'CREATE TABLE PURCHASE (
    PRODUCEID INT,
    FSID INT,
    ORDERID INT,
    PRIMARY KEY (PRODUCEID),
    FOREIGN KEY (PRODUCEID) REFERENCES CONTAININGREDIENT(PRODUCEID),
    FOREIGN KEY (FSID) REFERENCES FOODSERVICEFROM(FSID)
)';

EXECUTE IMMEDIATE 'CREATE TABLE NUTRITIONBENEFITS (
    CHEMNAME VARCHAR(150) PRIMARY KEY,
      VARCHAR(150)
)';

EXECUTE IMMEDIATE 'CREATE TABLE INCLUDENUTRITION (
   PRODUCEID INT,
   CHEMNAME VARCHAR(150),
   AMOUNT INT,
   PRIMARY KEY (PRODUCEID, CHEMNAME),
   FOREIGN KEY (PRODUCEID) REFERENCES CONTAININGREDIENT(PRODUCEID) ON DELETE CASCADE,
   FOREIGN KEY (CHEMNAME) REFERENCES NUTRITIONBENEFITS(CHEMNAME)
)';

EXECUTE IMMEDIATE 'CREATE TABLE INGREDIENTEXPIRATION (
    INAME CHAR(20),
    ORIGIN CHAR(20),
    MFGDATE DATE,
    EXPIRATION DATE,
    PRIMARY KEY (INAME, ORIGIN, MFGDATE),
    FOREIGN KEY (INAME, ORIGIN, MFGDATE) REFERENCES CONTAININGREDIENT(INAME,ORIGIN, MFGDATE)
)';

EXECUTE IMMEDIATE 'CREATE TABLE DISEASESOLUTIONS (
    TYPE CHAR(20) PRIMARY KEY,
    SOLUTION VARCHAR(150)
)';

EXECUTE IMMEDIATE 'CREATE TABLE AGRICULTURALDISEASE (
    ADNAME CHAR(30) PRIMARY KEY,
    TYPE CHAR(20),
    HEALTHEFFECT VARCHAR(150),
    FOREIGN KEY (TYPE) REFERENCES DISEASESOLUTIONS(TYPE)
)';

EXECUTE IMMEDIATE 'CREATE TABLE AFFECT (
    ADNAME CHAR(30),
    PRODUCEID INT,
    SEVERENESS INT,
    AFFECTDATE DATE,
    PRIMARY KEY (ADNAME, PRODUCEID),
    FOREIGN KEY (ADNAME) REFERENCES AGRICULTURALDISEASE(ADNAME),
    FOREIGN KEY (PRODUCEID) REFERENCES CONTAININGREDIENT(PRODUCEID)
)';

EXECUTE IMMEDIATE 'CREATE TABLE SYMPTOMEFFECTS (
    SYMPTOM VARCHAR(150) PRIMARY KEY,
    EFFECT VARCHAR(150)
)';

EXECUTE IMMEDIATE 'CREATE TABLE HEALTHISSUE (
    ISSUENAME VARCHAR(50) PRIMARY KEY,
    SYMPTOM VARCHAR(150),
    FOREIGN KEY (SYMPTOM) REFERENCES SYMPHOMEEFFECTS(SYMPTOM)
)';

EXECUTE IMMEDIATE 'CREATE TABLE MEDICINE (
    DIN INT PRIMARY KEY,
    MANUFACTURER CHAR(20),
    DNAME CHAR(20),
    EXPIRATION DATE,
    UNIQUE (MANUFACTURER, DNAME)
)';

EXECUTE IMMEDIATE 'CREATE TABLE CAUSE (
    FPNAME CHAR(20),
    ISSUENAME VARCHAR(50),
    EMERGENCYLEVEL CHAR(20),
    PRIMARY KEY (FPNAME, ISSUENAME),
    FOREIGN KEY (FPNAME) REFERENCES FOODPRODUCTSELL(FPNAME),
    FOREIGN KEY (ISSUENAME) REFERENCES HEALTHISSUE(ISSUENAME)
)';

EXECUTE IMMEDIATE 'CREATE TABLE TREATMENT (
    ISSUENAME VARCHAR(50),
    DIN INT,
    DOSE VARCHAR(150),
    PRIMARY KEY (ISSUENAME, DIN),
    FOREIGN KEY (ISSUENAME) REFERENCES HEALTHISSUE(ISSUENAME),
    FOREIGN KEY (DIN) REFERENCES MEDICINE(DIN)
)';




-- Inserting data into  Country
-- INSERT INTO Country (CountryName, Population, Continent) VALUES ('United States', 331002651, 'North America');
-- INSERT INTO Country (CountryName, Population, Continent) VALUES ('China', 1439323776, 'Asia');
-- INSERT INTO Country (CountryName, Population, Continent) VALUES ('India', 1380004385, 'Asia');
-- INSERT INTO Country (CountryName, Population, Continent) VALUES ('Brazil', 212559417, 'South America');
-- INSERT INTO Country (CountryName, Population, Continent) VALUES ('Russia', 145934462, 'Europe');
-- INSERT INTO Country (CountryName, Population, Continent) VALUES ('Nigeria', 206139589, 'Africa');
-- INSERT INTO Country (CountryName, Population, Continent) VALUES ('Japan', 126476461, 'Asia');
-- INSERT INTO Country (CountryName, Population, Continent) VALUES ('Mexico', 128932753, 'North America');
-- INSERT INTO Country (CountryName, Population, Continent) VALUES ('Germany', 83783942, 'Europe');
-- INSERT INTO Country (CountryName, Population, Continent) VALUES ('Egypt', 102334404, 'Africa');
-- INSERT INTO Country (CountryName, Population, Continent) VALUES ('Canada', 38250000, 'North America');

-- -- Inserting data into FoodServiceFrom
-- INSERT INTO FoodServiceFrom (FSID, FSName, FSAddress, hours, CountryName)
-- VALUES (1, 'Tasty Burgers', '123 Main Street, Anytown', '9am - 10pm', 'United States');

-- INSERT INTO FoodServiceFrom (FSID, FSName, FSAddress, hours, CountryName)
-- VALUES (2, 'Sushi Delight', '456 Maple Avenue, Othertown', '11am - 9pm', 'Japan');

-- INSERT INTO FoodServiceFrom (FSID, FSName, FSAddress, hours, CountryName)
-- VALUES (3, 'La Pizzeria', '789 Oak Street, Anycity', '12pm - 11pm', 'Canada');

-- INSERT INTO FoodServiceFrom (FSID, FSName, FSAddress, hours, CountryName)
-- VALUES (4, 'Taco Haven', '321 Elm Street, Someville', '10am - 8pm', 'Mexico');

-- INSERT INTO FoodServiceFrom (FSID, FSName, FSAddress, hours, CountryName)
-- VALUES (5, 'Curry House', '567 Pine Street, Anothercity', '11:30am - 10:30pm', 'India');

-- INSERT INTO FoodServiceFrom (FSID, FSName, FSAddress, hours, CountryName)
-- VALUES (6, 'Fresh Mart', '100 Oak Avenue, Anytown', '8am - 9pm', 'United States');

-- INSERT INTO FoodServiceFrom (FSID, FSName, FSAddress, hours, CountryName)
-- VALUES (7, 'Green Grocers', '200 Maple Street, Othertown', '9am - 8pm', 'Canada');

-- INSERT INTO FoodServiceFrom (FSID, FSName, FSAddress, hours, CountryName)
-- VALUES (8, 'Super Savers', '300 Elm Road, Somewhere', '7am - 10pm', 'United States');

-- INSERT INTO FoodServiceFrom (FSID, FSName, FSAddress, hours, CountryName)
-- VALUES (9, 'Quick Mart', '400 Pine Drive, Anothercity', '6am - 11pm', 'China');

-- INSERT INTO FoodServiceFrom (FSID, FSName, FSAddress, hours, CountryName)
-- VALUES (10, 'Neighborhood Market', '500 Cedar Lane, Smalltown', '10am - 7pm', 'Germany');

-- -- Inserting data into FoodProductSell table
-- INSERT INTO FoodProductSell (FPName, Description, Recipe, FSID)
-- VALUES ('Cheeseburger', 'Classic beef burger topped with cheese, lettuce, and tomato', 'Beef patty, cheese, lettuce, tomato, burger bun', 1);

-- INSERT INTO FoodProductSell (FPName, Description, Recipe, FSID)
-- VALUES ('French Fries', 'Crispy golden French fries, perfect as a side dish', 'Potatoes, oil, salt', 1);

-- INSERT INTO FoodProductSell (FPName, Description, Recipe, FSID)
-- VALUES ('California Roll', 'Sushi roll filled with crab, avocado, and cucumber', 'Rice, crab, avocado, cucumber, seaweed', 2);

-- INSERT INTO FoodProductSell (FPName, Description, Recipe, FSID)
-- VALUES ('Miso Soup', 'Traditional Japanese soup made with miso paste and tofu', 'Dashi broth, miso paste, tofu, green onions', 2);

-- INSERT INTO FoodProductSell (FPName, Description, Recipe, FSID)
-- VALUES ('Margherita Pizza', 'Traditional Italian pizza with tomato sauce, mozzarella, and basil', 'Pizza dough, tomato sauce, mozzarella cheese, basil', 3);

-- INSERT INTO FoodProductSell (FPName, Description, Recipe, FSID)
-- VALUES ('Guacamole', 'Creamy avocado dip with tomatoes, onions, and cilantro', 'Avocado, tomatoes, onions, cilantro, lime juice', 4);

-- INSERT INTO FoodProductSell (FPName, Description, Recipe, FSID)
-- VALUES ('Tacos al Pastor', 'Mexican tacos filled with marinated pork, pineapple, and onions', 'Marinated pork, pineapple, onions, tortillas', 4);

-- INSERT INTO FoodProductSell (FPName, Description, Recipe, FSID)
-- VALUES ('Chicken Tikka Masala', 'Indian dish with grilled chicken in a creamy tomato sauce', 'Chicken, yogurt, tomatoes, spices, cream', 5);

-- INSERT INTO FoodProductSell (FPName, Description, Recipe, FSID)
-- VALUES ('Ground Beef', 'Freshly ground beef ideal for burgers and meatballs', 'Beef, seasoning', 6);

-- INSERT INTO FoodProductSell (FPName, Description, Recipe, FSID)
-- VALUES ('Tomatoes', 'Ripe and juicy tomatoes, perfect for salads and cooking', 'Tomatoes', 6);

-- INSERT INTO FoodProductSell (FPName, Description, Recipe, FSID)
-- VALUES ('Salmon Fillet', 'Premium quality salmon fillet, perfect for grilling or baking', 'Salmon', 7);

-- INSERT INTO FoodProductSell (FPName, Description, Recipe, FSID)
-- VALUES ('Whole Wheat Bread', 'Nutritious whole wheat bread, great for sandwiches and toasting', 'Whole wheat flour, water, yeast, salt', 8);

-- INSERT INTO FoodProductSell (FPName, Description, Recipe, FSID)
-- VALUES ('Apples', 'Fresh, juicy apples sourced locally', 'Apples', 9);

-- INSERT INTO FoodProductSell (FPName, Description, Recipe, FSID)
-- VALUES ('Organic Milk', 'Certified organic milk, free from antibiotics and hormones', 'Milk', 10);

-- INSERT INTO FoodProductSell (FPName, Description, Recipe, FSID)
-- VALUES ('Yogurt', 'Creamy yogurt, available in various flavors', 'Milk, cultures', 10);

-- INSERT INTO FoodProductSell (FPName, Description, Recipe, FSID)
-- VALUES ('Orange Juice', 'Freshly squeezed orange juice, rich in vitamin C', 'Oranges', 10);


-- -- Inserting data into GroceryStore table
-- INSERT INTO GroceryStore (FSID, GSLicense, Departments)
-- VALUES (6, 123456, 10);

-- INSERT INTO GroceryStore (FSID, GSLicense, Departments)
-- VALUES (7, 789012, 8);

-- INSERT INTO GroceryStore (FSID, GSLicense, Departments)
-- VALUES (8, 345678, 12);

-- INSERT INTO GroceryStore (FSID, GSLicense, Departments)
-- VALUES (9, 901234, 6);

-- INSERT INTO GroceryStore (FSID, GSLicense, Departments)
-- VALUES (10, 567890, 9);

-- INSERT INTO Restaurant (FSID, RLicense, Tables)
-- VALUES (1, 1234567, 20);

-- INSERT INTO Restaurant (FSID, RLicense, Tables)
-- VALUES (2, 2345678, 15);

-- INSERT INTO Restaurant (FSID, RLicense, Tables)
-- VALUES (3, 3456789, 25);

-- INSERT INTO Restaurant (FSID, RLicense, Tables)
-- VALUES (4, 4567890, 18);

-- INSERT INTO Restaurant (FSID, RLicense, Tables)
-- VALUES (5, 5678901, 30);

-- -- Inserting data into ContainIngredient table
-- INSERT INTO ContainIngredient (ProduceID, IName, Origin, MFGDate, FPName)
-- VALUES (1, 'Beef patty', 'Local farm', DATE '2024-03-01', 'Cheeseburger');

-- INSERT INTO ContainIngredient (ProduceID, IName, Origin, MFGDate, FPName)
-- VALUES (2, 'Cheese', 'Dairy farm', DATE '2024-02-28', 'Cheeseburger');

-- INSERT INTO ContainIngredient (ProduceID, IName, Origin, MFGDate, FPName)
-- VALUES (3, 'Lettuce', 'Local farm', DATE '2024-03-02', 'Cheeseburger');

-- INSERT INTO ContainIngredient (ProduceID, IName, Origin, MFGDate, FPName)
-- VALUES (4, 'Tomato', 'Local farm', DATE '2024-03-03', 'Cheeseburger');

-- INSERT INTO ContainIngredient (ProduceID, IName, Origin, MFGDate, FPName)
-- VALUES (5, 'Potatoes', 'Local farm', DATE '2024-03-01', 'French Fries');

-- INSERT INTO ContainIngredient (ProduceID, IName, Origin, MFGDate, FPName)
-- VALUES (6, 'Oil', 'Local supplier', DATE '2024-02-28', 'French Fries');

-- INSERT INTO ContainIngredient (ProduceID, IName, Origin, MFGDate, FPName)
-- VALUES (7, 'Salt', 'Natural deposits', DATE '2024-02-27', 'French Fries');

-- INSERT INTO ContainIngredient (ProduceID, IName, Origin, MFGDate, FPName)
-- VALUES (8, 'Crab', 'Coastal waters', DATE '2024-03-01', 'California Roll');

-- INSERT INTO ContainIngredient (ProduceID, IName, Origin, MFGDate, FPName)
-- VALUES (9, 'Cucumber', 'Local farm', DATE '2024-03-03', 'California Roll');

-- INSERT INTO ContainIngredient (ProduceID, IName, Origin, MFGDate, FPName)
-- VALUES (10, 'Seaweed', 'Coastal waters', DATE '2024-02-28', 'California Roll');

-- INSERT INTO ContainIngredient (ProduceID, IName, Origin, MFGDate, FPName)
-- VALUES (11, 'Miso paste', 'Local supplier', DATE '2024-03-01', 'Miso Soup');

-- INSERT INTO ContainIngredient (ProduceID, IName, Origin, MFGDate, FPName)
-- VALUES (12, 'Mozzarella cheese', 'Dairy farm', DATE '2024-02-29', 'Margherita Pizza');

-- INSERT INTO ContainIngredient (ProduceID, IName, Origin, MFGDate, FPName)
-- VALUES (13, 'Avocado', 'Local farm', DATE '2024-03-01', 'Guacamole');

-- INSERT INTO ContainIngredient (ProduceID, IName, Origin, MFGDate, FPName)
-- VALUES (14, 'Tomatoes', 'Local farm', DATE '2024-03-02', 'Guacamole');

-- INSERT INTO ContainIngredient (ProduceID, IName, Origin, MFGDate, FPName)
-- VALUES (15, 'Marinated pork', 'Local butcher', DATE '2024-03-01', 'Tacos al Pastor');

-- -- Inserting data into Purchase table
-- INSERT INTO Purchase (ProduceID, FSID, OrderID)
-- VALUES (1, 1, 1);

-- INSERT INTO Purchase (ProduceID, FSID, OrderID)
-- VALUES (2, 1, 1);

-- INSERT INTO Purchase (ProduceID, FSID, OrderID)
-- VALUES (3, 1, 1);

-- INSERT INTO Purchase (ProduceID, FSID, OrderID)
-- VALUES (4, 1, 1);

-- INSERT INTO Purchase (ProduceID, FSID, OrderID)
-- VALUES (5, 1, 2);

-- INSERT INTO Purchase (ProduceID, FSID, OrderID)
-- VALUES (6, 1, 2);

-- INSERT INTO Purchase (ProduceID, FSID, OrderID)
-- VALUES (7, 1, 2);

-- INSERT INTO Purchase (ProduceID, FSID, OrderID)
-- VALUES (8, 2, 3);

-- INSERT INTO Purchase (ProduceID, FSID, OrderID)
-- VALUES (9, 2, 3);

-- INSERT INTO Purchase (ProduceID, FSID, OrderID)
-- VALUES (10, 2, 3);

-- INSERT INTO Purchase (ProduceID, FSID, OrderID)
-- VALUES (11, 2, 4);

-- INSERT INTO Purchase (ProduceID, FSID, OrderID)
-- VALUES (12, 3, 5);

-- INSERT INTO Purchase (ProduceID, FSID, OrderID)
-- VALUES (13, 4, 6);

-- INSERT INTO Purchase (ProduceID, FSID, OrderID)
-- VALUES (14, 4, 6);

-- INSERT INTO Purchase (ProduceID, FSID, OrderID)
-- VALUES (15, 4, 7);

-- -- Inserting data into NutritionBenefits table
-- INSERT INTO NutritionBenefits (ChemName, Benefits)
-- VALUES ('Protein', 'Promotes muscle growth and repair, helps in enzyme and hormone production');

-- INSERT INTO NutritionBenefits (ChemName, Benefits)
-- VALUES ('Fat', 'Provides energy, supports cell growth, protects organs, helps absorb certain vitamins');

-- INSERT INTO NutritionBenefits (ChemName, Benefits)
-- VALUES ('Carbohydrates', 'Main source of energy, essential for brain function and physical activity');

-- -- Inserting data into IncludeNutrition table
-- INSERT INTO IncludeNutrition (ProduceID, ChemName, Amount)
-- VALUES (1, 'Protein', 20);

-- INSERT INTO IncludeNutrition (ProduceID, ChemName, Amount)
-- VALUES (1, 'Fat', 15);

-- INSERT INTO IncludeNutrition (ProduceID, ChemName, Amount)
-- VALUES (1, 'Carbohydrates', 5);

-- INSERT INTO IncludeNutrition (ProduceID, ChemName, Amount)
-- VALUES (2, 'Protein', 10);

-- INSERT INTO IncludeNutrition (ProduceID, ChemName, Amount)
-- VALUES (2, 'Fat', 12);

-- INSERT INTO IncludeNutrition (ProduceID, ChemName, Amount)
-- VALUES (2, 'Carbohydrates', 3);

-- INSERT INTO IncludeNutrition (ProduceID, ChemName, Amount)
-- VALUES (3, 'Protein', 1);

-- INSERT INTO IncludeNutrition (ProduceID, ChemName, Amount)
-- VALUES (3, 'Carbohydrates', 2);

-- INSERT INTO IncludeNutrition (ProduceID, ChemName, Amount)
-- VALUES (4, 'Protein', 1);

-- INSERT INTO IncludeNutrition (ProduceID, ChemName, Amount)
-- VALUES (4, 'Carbohydrates', 3);

-- INSERT INTO IncludeNutrition (ProduceID, ChemName, Amount)
-- VALUES (5, 'Protein', 2);

-- INSERT INTO IncludeNutrition (ProduceID, ChemName, Amount)
-- VALUES (5, 'Carbohydrates', 20);

-- INSERT INTO IncludeNutrition (ProduceID, ChemName, Amount)
-- VALUES (6, 'Protein', 0);

-- INSERT INTO IncludeNutrition (ProduceID, ChemName, Amount)
-- VALUES (6, 'Fat', 30);

-- INSERT INTO IncludeNutrition (ProduceID, ChemName, Amount)
-- VALUES (7, 'Protein', 0);

-- -- Inserting data into IngredientExpiration table
-- INSERT INTO IngredientExpiration (IName, Origin, MFGDate, Expiration)
-- VALUES ('Beef patty', 'Local farm', DATE '2024-03-01', DATE '2024-03-15');

-- INSERT INTO IngredientExpiration (IName, Origin, MFGDate, Expiration)
-- VALUES ('Cheese', 'Dairy farm', DATE '2024-02-28', DATE '2024-03-14');

-- INSERT INTO IngredientExpiration (IName, Origin, MFGDate, Expiration)
-- VALUES ('Lettuce', 'Local farm', DATE '2024-03-02', DATE '2024-03-16');

-- INSERT INTO IngredientExpiration (IName, Origin, MFGDate, Expiration)
-- VALUES ('Tomato', 'Local farm', DATE '2024-03-03', DATE '2024-03-17');

-- INSERT INTO IngredientExpiration (IName, Origin, MFGDate, Expiration)
-- VALUES ('Oil', 'Local supplier', DATE '2024-02-28', DATE '2025-02-28');

-- INSERT INTO IngredientExpiration (IName, Origin, MFGDate, Expiration)
-- VALUES ('Salt', 'Natural deposits', DATE '2024-02-27', NULL);

-- INSERT INTO IngredientExpiration (IName, Origin, MFGDate, Expiration)
-- VALUES ('Crab', 'Coastal waters', DATE '2024-03-01', DATE '2024-03-10');

-- INSERT INTO IngredientExpiration (IName, Origin, MFGDate, Expiration)
-- VALUES ('Seaweed', 'Coastal waters', DATE '2024-02-28', DATE '2024-03-14');

-- INSERT INTO IngredientExpiration (IName, Origin, MFGDate, Expiration)
-- VALUES ('Miso paste', 'Local supplier', DATE '2024-03-01', DATE '2025-03-01');

-- INSERT INTO IngredientExpiration (IName, Origin, MFGDate, Expiration)
-- VALUES ('Mozzarella cheese', 'Dairy farm', DATE '2024-02-29', DATE '2024-03-14');

-- INSERT INTO IngredientExpiration (IName, Origin, MFGDate, Expiration)
-- VALUES ('Marinated pork', 'Local butcher', DATE '2024-03-01', DATE '2024-03-08');

-- -- Inserting data into DiseaseSolutions table
-- INSERT INTO DiseaseSolutions (Type, Solution)
-- VALUES ('Natural Hazard', 'Implement proper drainage systems to mitigate flooding risks.');

-- INSERT INTO DiseaseSolutions (Type, Solution)
-- VALUES ('Chemical', 'Use organic pesticides and herbicides to minimize chemical exposure.');

-- INSERT INTO DiseaseSolutions (Type, Solution)
-- VALUES ('Biological', 'Introduce natural predators to control pest populations.');

-- INSERT INTO DiseaseSolutions (Type, Solution)
-- VALUES ('Virus', 'Implement strict biosecurity measures to prevent disease transmission.');

-- -- Inserting data into AgriculturalDisease table
-- INSERT INTO AgriculturalDisease (ADName, Type, HealthEffect)
-- VALUES ('Flood Damage', 'Natural Hazard', 'Loss of crops and soil erosion due to flooding.');

-- INSERT INTO AgriculturalDisease (ADName, Type, HealthEffect)
-- VALUES ('Pesticide Toxicity', 'Chemical', NULL);

-- INSERT INTO AgriculturalDisease (ADName, Type, HealthEffect)
-- VALUES ('Insect Infestation', 'Biological', 'Crop damage caused by pest insects.');

-- INSERT INTO AgriculturalDisease (ADName, Type, HealthEffect)
-- VALUES ('BSE', 'Biological', 'Might destroy the human brain and spinal cord');

-- INSERT INTO AgriculturalDisease (ADName, Type, HealthEffect)
-- VALUES ('Tomato Spotted Wilt Virus', 'Virus', 'Unknown');

-- -- Inserting data into Affect table
-- INSERT INTO Affect (ADName, ProduceID, Severeness, AffectDate)
-- VALUES ('BSE', 1, 10, DATE '2024-03-15');

-- INSERT INTO Affect (ADName, ProduceID, Severeness, AffectDate)
-- VALUES ('Flood Damage', 5, 8, DATE '2024-04-11');

-- INSERT INTO Affect (ADName, ProduceID, Severeness, AffectDate)
-- VALUES ('Insect Infestation', 3, 6, DATE '2024-05-10');

-- INSERT INTO Affect (ADName, ProduceID, Severeness, AffectDate)
-- VALUES ('Tomato Spotted Wilt Virus', 4, 7, DATE '2024-05-21');

-- -- Inserting more data into SymptomEffects table
-- INSERT INTO SymptomEffects (Symptom, Effect)
-- VALUES ('Nausea', 'Feeling of sickness in the stomach, often leading to vomiting.');

-- INSERT INTO SymptomEffects (Symptom, Effect)
-- VALUES ('Diarrhea', 'Frequent passage of loose, watery stools.');

-- INSERT INTO SymptomEffects (Symptom, Effect)
-- VALUES ('Abdominal Pain', 'Pain or discomfort in the area between the chest and pelvis.');

-- INSERT INTO SymptomEffects (Symptom, Effect)
-- VALUES ('Skin Rash', 'Red, itchy rash on the skin.');

-- INSERT INTO SymptomEffects (Symptom, Effect)
-- VALUES ('Swelling', 'Swelling of the face, lips, tongue, or throat.');

-- INSERT INTO SymptomEffects (Symptom, Effect)
-- VALUES ('Difficulty Breathing', 'Shortness of breath, wheezing, or tightness in the chest.');

-- INSERT INTO SymptomEffects (Symptom, Effect)
-- VALUES ('Difficulty with Movement', 'Hard to move.');

-- Inserting more data into HealthIssue table
-- INSERT INTO HealthIssue (IssueName, Symptom)
-- VALUES ('Food Poisoning', 'Nausea');

-- INSERT INTO HealthIssue (IssueName, Symptom)
-- VALUES ('Gastroenteritis', 'Diarrhea');

-- INSERT INTO HealthIssue (IssueName, Symptom)
-- VALUES ('Obesity', 'Difficulty with Movement');

-- INSERT INTO HealthIssue (IssueName, Symptom)
-- VALUES ('Irritable Bowel Syndrome (IBS)', 'Abdominal Pain');

-- INSERT INTO HealthIssue (IssueName, Symptom)
-- VALUES ('Gluten Intolerance', 'Diarrhea');

-- INSERT INTO HealthIssue (IssueName, Symptom)
-- VALUES ('Nut Allergy', 'Skin Rash');

-- INSERT INTO HealthIssue (IssueName, Symptom)
-- VALUES ('Seafood Allergy', 'Swelling');

-- -- Inserting data into Medicine table
-- INSERT INTO Medicine (DIN, Manufacturer, DName, Expiration)
-- VALUES (123456, 'ABC Pharmaceuticals', 'Antacid', DATE '2024-12-31');

-- INSERT INTO Medicine (DIN, Manufacturer, DName, Expiration)
-- VALUES (234567, 'DEF Labs', 'Anti-diarrheal', DATE '2025-06-30');

-- INSERT INTO Medicine (DIN, Manufacturer, DName, Expiration)
-- VALUES (345678, 'MediCo', 'IBS Relief', DATE '2023-10-15');

-- INSERT INTO Medicine (DIN, Manufacturer, DName, Expiration)
-- VALUES (456789, 'Global Health', 'EpiPen', DATE '2024-08-20');

-- INSERT INTO Medicine (DIN, Manufacturer, DName, Expiration)
-- VALUES (567890, 'PharmaCorp', 'Antihistamine', DATE '2024-05-15');

-- -- Inserting data into Cause table
-- INSERT INTO Cause (FPName, IssueName, EmergencyLevel)
-- VALUES ('Cheeseburger', 'Food Poisoning', 'High');

-- INSERT INTO Cause (FPName, IssueName, EmergencyLevel)
-- VALUES ('French Fries', 'Obesity', 'Low');

-- INSERT INTO Cause (FPName, IssueName, EmergencyLevel)
-- VALUES ('California Roll', 'Seafood Allergy', 'High');

-- INSERT INTO Cause (FPName, IssueName, EmergencyLevel)
-- VALUES ('Miso Soup', 'Gastroenteritis', 'Medium');

-- INSERT INTO Cause (FPName, IssueName, EmergencyLevel)
-- VALUES ('Margherita Pizza', 'Gluten Intolerance', 'Low');

-- INSERT INTO Cause (FPName, IssueName, EmergencyLevel)
-- VALUES ('Guacamole', 'Nut Allergy', 'High');

-- -- Inserting data into Treatment table
-- INSERT INTO Treatment (IssueName, DIN, Dose)
-- VALUES ('Food Poisoning', 123456, 'Take 2 tablets after meals');

-- INSERT INTO Treatment (IssueName, DIN, Dose)
-- VALUES ('Gastroenteritis', 234567, 'Take 1 tablet every 6 hours as needed');

-- INSERT INTO Treatment (IssueName, DIN, Dose)
-- VALUES ('Irritable Bowel Syndrome (IBS)', 345678, 'Take 1 capsule daily with water');

-- INSERT INTO Treatment (IssueName, DIN, Dose)
-- VALUES ('Nut Allergy', 456789, 'Administer intramuscularly at onset of symptoms');

-- INSERT INTO Treatment (IssueName, DIN, Dose)
-- VALUES ('Seafood Allergy', 456789, 'Administer intramuscularly at onset of symptoms');

-- -- UPDATE Country SET CountryName = UPPER(CountryName), Continent = UPPER(Continent);
-- -- UPDATE FoodServiceFrom SET FSName = UPPER(FSName), FSAddress = UPPER(FSAddress), Hours = UPPER(Hours);
-- -- UPDATE FoodProductSell SET FPName = UPPER(FPName), Description = UPPER(Description), Recipe = UPPER(Recipe);
-- -- UPDATE GroceryStore SET GSLicense = UPPER(GSLicense);
-- -- UPDATE Restaurant SET RLicense = UPPER(RLicense);
-- -- UPDATE ContainIngredient SET IName = UPPER(IName), Origin = UPPER(Origin);
-- -- UPDATE NutritionBenefits SET ChemName = UPPER(ChemName), Benefits = UPPER(Benefits);
-- -- UPDATE IngredientExpiration SET IName = UPPER(IName), Origin = UPPER(Origin);
-- -- UPDATE DiseaseSolutions SET Type = UPPER(Type), Solution = UPPER(Solution);
-- -- UPDATE AgriculturalDisease SET ADName = UPPER(ADName), Type = UPPER(Type), HealthEffect = UPPER(HealthEffect);
-- -- UPDATE SymptomEffects SET Symptom = UPPER(Symptom), Effect = UPPER(Effect);
-- -- UPDATE HealthIssue SET IssueName = UPPER(IssueName), Symptom = UPPER(Symptom);
-- -- UPDATE Cause SET FPName = UPPER(FPName), IssueName = UPPER(IssueName), EmergencyLevel = UPPER(EmergencyLevel);
-- -- UPDATE Treatment SET IssueName = UPPER(IssueName);


 END;