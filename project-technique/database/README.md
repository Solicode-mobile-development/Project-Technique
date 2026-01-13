# Database Setup - Real Estate Platform

## Overview
This database structure is based on the class diagram in `diagrams/class-diagram.pu` and supports a real estate listing platform.

## Database Structure

### Tables Created

1. **users**
   - `id` - Primary key
   - `username` - Unique username
   - `password` - Hashed password
   - `role` - Enum ('admin', 'user')
   - `remember_token` - For "remember me" functionality
   - `timestamps` - created_at, updated_at

2. **categories**
   - `id` - Primary key
   - `name` - Unique category name
   - `slug` - URL-friendly slug
   - `description` - Category description
   - `timestamps`

3. **properties**
   - `id` - Primary key
   - `title` - Property title
   - `description` - Detailed description
   - `price` - Decimal price
   - `city` - Location city
   - `image_path` - Path to property image
   - `user_id` - Foreign key to users
   - `timestamps`

4. **category_property** (Pivot Table)
   - `id` - Primary key
   - `category_id` - Foreign key to categories
   - `property_id` - Foreign key to properties
   - `timestamps`
   - Unique constraint on (category_id, property_id)

### Relationships

- **User → Property**: One-to-Many (one user can post multiple properties)
- **Property ↔ Category**: Many-to-Many (properties can belong to multiple categories)

## Installation & Usage

### 1. Run Migrations

```bash
# Fresh migration (drops all tables and recreates)
php artisan migrate:fresh

# Or regular migration
php artisan migrate
```

### 2. Seed the Database

```bash
# Seed all data
php artisan db:seed

# Or seed specific seeders
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=PropertySeeder

# Fresh migration + seed in one command
php artisan migrate:fresh --seed
```

## CSV Data Files

The seeders read from CSV files located in `database/data/`:

### users.csv
Format: `username,password,role`

Sample data includes:
- 1 admin user
- 3 regular users

**Note**: Passwords are automatically hashed when seeded.

### categories.csv
Format: `name,slug,description`

Sample categories:
- Appartement
- Maison
- Villa
- Studio
- Terrain

### properties.csv
Format: `title,description,price,city,image_path,user_username,categories`

Sample data includes 10 properties across different Moroccan cities:
- Casablanca
- Marrakech
- Rabat
- Fès
- Tanger
- Agadir
- Meknès

**Note**: 
- The `categories` field can contain multiple categories separated by `|` (pipe)
- The `user_username` field references the username from users.csv

## Customizing Data

To add or modify data:

1. Edit the CSV files in `database/data/`
2. Follow the format shown in the existing files
3. Run `php artisan migrate:fresh --seed` to reset and reseed

## Sample Data

### Users
- **admin** (role: admin) - password: secret123
- **john_doe** (role: user) - password: password123
- **jane_smith** (role: user) - password: mypass456
- **robert_jones** (role: user) - password: secure789

### Categories
- Appartement - Modern and spacious apartments
- Maison - Individual houses with gardens
- Villa - Luxury villas with pools
- Studio - Compact studios for students
- Terrain - Buildable land for sale

### Properties
10 diverse property listings including apartments, villas, studios, houses, and land across Morocco.

## Migration Order

The migrations run in this order (important for foreign key constraints):

1. `0001_01_01_000000_create_users_table`
2. `0001_01_01_000001_create_cache_table`
3. `0001_01_01_000002_create_jobs_table`
4. `2024_01_01_000003_create_categories_table`
5. `2024_01_01_000004_create_properties_table`
6. `2024_01_01_000005_create_category_property_table`

## Seeder Order

Seeders must run in this order:

1. UserSeeder (users must exist before properties)
2. CategorySeeder (categories must exist before property-category relations)
3. PropertySeeder (creates properties and their category relations)

## Troubleshooting

### Migration Errors

If you encounter foreign key errors:
- Ensure migrations run in the correct order
- Check that the `properties` table is created before `category_property`

### Seeding Errors

If seeding fails:
- Verify CSV files exist in `database/data/`
- Check CSV format matches the expected columns
- Ensure usernames in properties.csv match those in users.csv
- Ensure category names in properties.csv match those in categories.csv

## Database Verification

After seeding, you should have:
- 4 users
- 5 categories
- 10 properties
- 10 category-property relationships
