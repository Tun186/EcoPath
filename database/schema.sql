-- Role and Permissions Module
CREATE TABLE Role (
    RoleID VARCHAR(50) PRIMARY KEY,
    RoleName VARCHAR(255) NOT NULL
);

CREATE TABLE Permission (
    PermissionID VARCHAR(50) PRIMARY KEY,
    PermissionName VARCHAR(255) NOT NULL
);

CREATE TABLE Role_Permission (
    RoleID VARCHAR(50),
    PermissionID VARCHAR(50),
    PRIMARY KEY (RoleID, PermissionID),
    FOREIGN KEY (RoleID) REFERENCES Role(RoleID),
    FOREIGN KEY (PermissionID) REFERENCES Permission(PermissionID)
);

CREATE TABLE Subscription_Tier (
    TierID VARCHAR(50) PRIMARY KEY,
    TierName VARCHAR(255) NOT NULL,
    TreeMultiplier DECIMAL(5,2),
    BasePrice DECIMAL(10,2)
);

CREATE TABLE User (
    UserID VARCHAR(50) PRIMARY KEY,
    RoleID VARCHAR(50),
    TierID VARCHAR(50),
    Username VARCHAR(255) NOT NULL,
    PasswordHash VARCHAR(255) NOT NULL,
    Email VARCHAR(255) NOT NULL UNIQUE,
    Phone VARCHAR(50),
    EcoPoints INT DEFAULT 0,
    RegistrationDate DATE,
    FOREIGN KEY (RoleID) REFERENCES Role(RoleID),
    FOREIGN KEY (TierID) REFERENCES Subscription_Tier(TierID)
);

-- Environmental Partner and Donation
CREATE TABLE Environmental_Partner (
    PartnerID VARCHAR(50) PRIMARY KEY,
    OrganizationName VARCHAR(255) NOT NULL,
    VerificationStatus VARCHAR(100)
);

CREATE TABLE Donation (
    DonationID VARCHAR(50) PRIMARY KEY,
    UserID VARCHAR(50),
    PartnerID VARCHAR(50),
    Amount DECIMAL(10,2),
    DonationDate DATE,
    FOREIGN KEY (UserID) REFERENCES User(UserID),
    FOREIGN KEY (PartnerID) REFERENCES Environmental_Partner(PartnerID)
);

-- Travel Infrastructure & Geography
CREATE TABLE Region (
    RegionID VARCHAR(50) PRIMARY KEY,
    RegionName VARCHAR(255) NOT NULL
);

CREATE TABLE City (
    CityID VARCHAR(50) PRIMARY KEY,
    RegionID VARCHAR(50),
    CityName VARCHAR(255) NOT NULL,
    FOREIGN KEY (RegionID) REFERENCES Region(RegionID)
);

CREATE TABLE Hotel (
    HotelID VARCHAR(50) PRIMARY KEY,
    CityID VARCHAR(50),
    HotelName VARCHAR(255) NOT NULL,
    EcoRating VARCHAR(100),
    Lat DECIMAL(10,8),
    Lng DECIMAL(11,8),
    Description TEXT,
    CreatedBy VARCHAR(50),
    UpdatedBy VARCHAR(50),
    UpdatedAt DATETIME,
    IsActive BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (CityID) REFERENCES City(CityID)
);

CREATE TABLE Landmarks (
    LandmarkID VARCHAR(50) PRIMARY KEY,
    CityID VARCHAR(50),
    LandmarkName VARCHAR(255) NOT NULL,
    Lat DECIMAL(10,8),
    Lng DECIMAL(11,8),
    Description TEXT,
    CreatedBy VARCHAR(50),
    UpdatedBy VARCHAR(50),
    UpdatedAt DATETIME,
    IsActive BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (CityID) REFERENCES City(CityID)
);

CREATE TABLE Driver (
    DriverID VARCHAR(50) PRIMARY KEY,
    DriverName VARCHAR(255) NOT NULL,
    LicenseCode VARCHAR(100)
);

CREATE TABLE Bus (
    BusID VARCHAR(50) PRIMARY KEY,
    DriverID VARCHAR(50),
    OperatorName VARCHAR(255),
    EmissionRate DECIMAL(5,2),
    FOREIGN KEY (DriverID) REFERENCES Driver(DriverID)
);

CREATE TABLE Bus_Seats (
    SeatID VARCHAR(50) PRIMARY KEY,
    BusID VARCHAR(50),
    SeatNumber VARCHAR(10),
    IsBooked BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (BusID) REFERENCES Bus(BusID)
);

CREATE TABLE Package (
    PackageID VARCHAR(50) PRIMARY KEY,
    BusID VARCHAR(50),
    PackageName VARCHAR(255) NOT NULL,
    BaseTreeCount INT,
    Price DECIMAL(10,2),
    Distance DECIMAL(10,2) DEFAULT 0,
    CalculatedCO2 DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (BusID) REFERENCES Bus(BusID)
);

CREATE TABLE Package_Hotel (
    PackageID VARCHAR(50),
    HotelID VARCHAR(50),
    PRIMARY KEY (PackageID, HotelID),
    FOREIGN KEY (PackageID) REFERENCES Package(PackageID),
    FOREIGN KEY (HotelID) REFERENCES Hotel(HotelID)
);

CREATE TABLE Package_Landmarks (
    PackageID VARCHAR(50),
    LandmarkID VARCHAR(50),
    PRIMARY KEY (PackageID, LandmarkID),
    FOREIGN KEY (PackageID) REFERENCES Package(PackageID),
    FOREIGN KEY (LandmarkID) REFERENCES Landmarks(LandmarkID)
);

-- E-Commerce & Financial
CREATE TABLE Transaction (
    TransactionID VARCHAR(50) PRIMARY KEY,
    UserID VARCHAR(50),
    PackageID VARCHAR(50),
    TotalAmount DECIMAL(10,2),
    Status VARCHAR(50),
    TransactionDate DATE,
    FOREIGN KEY (UserID) REFERENCES User(UserID),
    FOREIGN KEY (PackageID) REFERENCES Package(PackageID)
);

CREATE TABLE Payment (
    PaymentID VARCHAR(50) PRIMARY KEY,
    TransactionID VARCHAR(50),
    Amount DECIMAL(10,2),
    PaymentMethod VARCHAR(50),
    PaymentDate DATE,
    FOREIGN KEY (TransactionID) REFERENCES Transaction(TransactionID)
);

CREATE TABLE Reward (
    RewardID VARCHAR(50) PRIMARY KEY,
    TransactionID VARCHAR(50),
    RewardDetails TEXT,
    FOREIGN KEY (TransactionID) REFERENCES Transaction(TransactionID)
);

CREATE TABLE Point_Exchange (
    ExchangeID VARCHAR(50) PRIMARY KEY,
    UserID VARCHAR(50),
    PointsUsed INT,
    DiscountApplied DECIMAL(10,2),
    ExchangeDate DATE,
    FOREIGN KEY (UserID) REFERENCES User(UserID)
);

-- Ecological Impact
CREATE TABLE Carbon_log (
    LogID VARCHAR(50) PRIMARY KEY,
    UserID VARCHAR(50) NULL,
    PackageID VARCHAR(50) NULL,
    CompanyID VARCHAR(50) NULL,
    CO2Emitted DECIMAL(10,2),
    TreesPlanted INT,
    LogDate DATE,
    FOREIGN KEY (UserID) REFERENCES User(UserID),
    FOREIGN KEY (PackageID) REFERENCES Package(PackageID)
    -- Foreign Key to CompanyID will be added below
);

-- B2B Corporate Carbon Credits
CREATE TABLE Company_Profile (
    CompanyID VARCHAR(50) PRIMARY KEY,
    CompanyName VARCHAR(255) NOT NULL,
    RegistrationNumber VARCHAR(100),
    ContactEmail VARCHAR(255),
    PurchasedCredits INT DEFAULT 0
);

ALTER TABLE Carbon_log 
ADD FOREIGN KEY (CompanyID) REFERENCES Company_Profile(CompanyID);

CREATE TABLE Company_Transaction (
    TransactionID VARCHAR(50) PRIMARY KEY,
    CompanyID VARCHAR(50),
    CreditsPurchased INT,
    TotalAmount DECIMAL(10,2),
    TransactionDate DATE,
    FOREIGN KEY (CompanyID) REFERENCES Company_Profile(CompanyID)
);
