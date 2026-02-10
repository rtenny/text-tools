# Town Assignment Feature - Implementation Plan

## 🎯 Objective

Extend the existing multi-tenant property description generation system to include a towns management feature that allows projects to have customizable lists of towns they service. This addresses the requirement that different projects operate in different areas of Spain and need different town selections.

## 📋 Requirements Summary

- Create a central towns table with all available towns
- Implement many-to-many relationship between projects and towns
- Allow project administrators to assign specific towns to their projects
- Update the property description generator to use project-specific towns
- Maintain multi-tenant data isolation

## 🗄️ Database Schema Changes

### New Table: `towns`
- `id` (INT, PRIMARY KEY, AUTO_INCREMENT) - Unique identifier for each town
- `name` (VARCHAR 255) - Name of the town
- `created_at` (DATETIME) - Timestamp of record creation
- `updated_at` (DATETIME) - Timestamp of last update

### Junction Table: `project_towns`
- `id` (INT, PRIMARY KEY, AUTO_INCREMENT) - Unique identifier for each relationship
- `project_id` (INT, FOREIGN KEY) - References projects.id with CASCADE delete
- `town_id` (INT, FOREIGN KEY) - References towns.id with CASCADE delete
- `created_at` (DATETIME) - Timestamp of assignment
- Index on (project_id, town_id) for performance

## 🔧 Implementation Approach

### Phase 1: Database Foundation (Day 1)
1. Create migration `2026-02-10-000005_CreateTownsTable.php`
2. Create migration `2026-02-10-000006_CreateProjectTownsTable.php`
3. Run migrations: `php spark migrate`
4. Create `TownModel.php` with basic CRUD operations
5. Create `ProjectTownModel.php` for managing project-town relationships
6. Create `TownsSeeder.php` with sample Spanish towns data

### Phase 2: Backend Services (Day 2)
1. Update `ProjectModel.php` to include methods for managing associated towns
2. Create `TownService.php` with methods:
   - `getTownsForProject(int $projectId): array`
   - `assignTownsToProject(int $projectId, array $townIds): bool`
   - `removeTownsFromProject(int $projectId, array $townIds): bool`
   - `getAllAvailableTowns(): array`
3. Update existing AI services to use project-specific towns in property generation

### Phase 3: Admin Interface Updates (Day 3)
1. Update `Superadmin/ProjectsController.php`:
   - Add methods for managing towns assignment: `assignTowns($projectId)`, `updateTowns($projectId)`
   - Add view for town assignment: `projects/towns.php`
2. Create new views in `app/Views/superadmin/projects/towns.php`:
   - Multi-select dropdown with all available towns
   - Shows currently assigned towns with checkboxes
   - Save button to update project-town relationships
3. Add routes for towns management in `Config/Routes.php`

### Phase 4: User Interface Integration (Day 4)
1. Update `Tools/GeneratorController.php`:
   - Modify the property generation form to load towns specific to the user's project
   - Update form validation to ensure selected town belongs to user's project
2. Update `app/Views/tools/index.php`:
   - Modify the town dropdown in the property generator tab to show only project-specific towns
   - Ensure proper filtering occurs based on session project_id
3. Add AJAX endpoint for dynamically loading project-specific towns

### Phase 5: Testing & Validation (Day 5)
1. Unit tests for new models and services
2. Integration tests for multi-tenant town isolation
3. End-to-end testing of town assignment and usage workflows
4. Verify that users can only see and select towns assigned to their project

## 🛠️ Technical Details

### Migration Files
```php
// 2026-02-10-000005_CreateTownsTable.php
- Create towns table with name field and timestamps
- Add unique constraint on town name to prevent duplicates

// 2026-02-10-000006_CreateProjectTownsTable.php
- Create junction table with foreign keys
- Add indexes for performance
- Implement CASCADE DELETE for referential integrity
```

### Model Updates
```php
// TownModel.php
- Basic CRUD operations
- Search functionality for town lookup
- Validation rules for town names

// ProjectTownModel.php
- Methods to get towns by project ID
- Methods to assign/remove towns from projects
- Validation to prevent duplicate assignments
```

### Controller Modifications
```php
// Superadmin/ProjectsController.php
- Add towns management methods
- Handle assignment of towns to projects
- Provide interfaces for town selection

// Tools/GeneratorController.php
- Update to use project-specific towns
- Add validation for town-project association
```

## 🧪 Testing Checklist

### Database Level
- [ ] Town creation/deletion works properly
- [ ] Project-town relationships are maintained correctly
- [ ] Cascade deletes work as expected
- [ ] Many-to-many relationship functions properly

### Business Logic
- [ ] Projects can have multiple towns assigned
- [ ] Towns can be shared across multiple projects
- [ ] Users only see towns assigned to their project
- [ ] Validation prevents unauthorized town access

### User Interface
- [ ] Town assignment interface works for superadmins
- [ ] Property generator shows correct towns for each project
- [ ] Form validation works with project-specific towns
- [ ] Error handling for invalid town selections

### Multi-tenancy
- [ ] Data isolation maintained between projects
- [ ] One project cannot access another project's towns
- [ ] Town assignments are properly scoped to projects

## 🔐 Security Considerations

1. **Access Control**: Ensure only superadmins can manage town assignments
2. **Data Validation**: Validate that submitted towns actually belong to the user's project
3. **Input Sanitization**: Sanitize town names during creation and updates
4. **Session Scoping**: Ensure town selection is properly limited by session project_id

## 🔄 Migration Strategy

1. **Backup**: Create database backup before applying migrations
2. **Apply Migrations**: Run new migrations to create tables
3. **Seed Data**: Populate towns table with initial Spanish towns
4. **Assign Default Towns**: Optionally assign common towns to existing projects
5. **Test Thoroughly**: Verify all functionality works as expected
6. **Deploy**: Apply to production environment with proper testing

## 🧩 Integration Points

### With Existing Features
- **Property Generator**: Town dropdown now shows project-specific options
- **AI Services**: Property generation uses project-specific town data
- **Multi-tenancy**: Maintains proper data isolation between projects
- **Authentication**: Leverages existing role-based access controls

### Frontend Changes
- Update the property generator form to dynamically load project-specific towns
- Maintain existing UX while restricting town options based on project
- Add loading indicators for dynamic town loading

## 📊 Sample Data

The towns seeder will include major Spanish towns across different regions:
- Andalusia: Marbella, Malaga, Seville, Granada
- Catalonia: Barcelona, Girona, Tarragona
- Valencia: Valencia, Alicante, Castellón
- Balearics: Palma, Port d'Andratx, Pollença
- Canary Islands: Las Palmas, Santa Cruz de Tenerife

## 🚨 Potential Challenges

1. **Performance**: Large number of towns might affect dropdown performance - implement search/filter functionality
2. **Data Consistency**: Ensuring town names are consistent across the system
3. **Migration Impact**: Existing projects will need to have towns assigned after implementation
4. **User Experience**: Making town assignment intuitive for superadmins

## 📈 Success Metrics

- [ ] Projects can successfully assign towns to their accounts
- [ ] Users only see towns assigned to their project in the generator
- [ ] Property descriptions correctly reference project-specific towns
- [ ] Multi-tenancy is maintained with no cross-project data leakage
- [ ] Performance remains acceptable with town filtering implemented