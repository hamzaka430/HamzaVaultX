# Contributing to HamzaVaultX

First off, thank you for considering contributing to HamzaVaultX! It's people like you that make HamzaVaultX such a great tool.

## Code of Conduct

This project and everyone participating in it is governed by respect and professionalism. By participating, you are expected to uphold this code.

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check existing issues to avoid duplicates. When you create a bug report, include as many details as possible:

- **Use a clear and descriptive title**
- **Describe the exact steps to reproduce the problem**
- **Provide specific examples to demonstrate the steps**
- **Describe the behavior you observed and what behavior you expected**
- **Include screenshots if relevant**
- **Specify your environment** (OS, browser, PHP version, Node version)

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion:

- **Use a clear and descriptive title**
- **Provide a detailed description of the suggested enhancement**
- **Explain why this enhancement would be useful**
- **List examples of where this enhancement exists in other tools** (if applicable)

### Pull Requests

1. **Fork the repository** and create your branch from `main`
2. **Follow the coding standards** outlined below
3. **Test your changes** thoroughly
4. **Update documentation** if needed
5. **Write clear commit messages**
6. **Submit your pull request**

## Development Setup

1. Fork and clone the repository
2. Follow the setup instructions in [README.md](README.md)
3. Create a new branch: `git checkout -b feature/your-feature-name`
4. Make your changes
5. Test thoroughly (both manually and with automated tests if applicable)
6. Commit using conventional commits (see below)
7. Push to your fork: `git push origin feature/your-feature-name`
8. Open a Pull Request

## Coding Standards

### Laravel (Backend)

- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards
- Use type hints and return types
- Write meaningful variable and method names
- Keep methods short and focused (Single Responsibility Principle)
- Use dependency injection over facades where appropriate
- Write PHPDoc comments for classes and methods

Example:
```php
/**
 * Store uploaded files to cloud storage
 *
 * @param FileUploadRequest $request
 * @return JsonResponse
 */
public function store(FileUploadRequest $request): JsonResponse
{
    // Implementation
}
```

### Vue 3 (Frontend)

- Use Composition API with `<script setup>`
- Follow [Vue.js Style Guide](https://vuejs.org/style-guide/)
- Use TypeScript-style prop definitions
- Keep components small and reusable
- Use descriptive component and variable names in camelCase
- Extract complex logic into composables

Example:
```vue
<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  file: Object,
  canEdit: Boolean
})

const isEditing = ref(false)
</script>
```

### Tailwind CSS (Styling)

- Use Tailwind utility classes
- Follow mobile-first responsive design
- Extract repeated patterns into components
- Avoid arbitrary values unless necessary
- Keep class lists organized and readable

## Git Commit Guidelines

We follow [Conventional Commits](https://www.conventionalcommits.org/) specification:

```
<type>(<scope>): <subject>

<body>

<footer>
```

### Types

- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, missing semicolons, etc.)
- `refactor`: Code refactoring
- `perf`: Performance improvements
- `test`: Adding or updating tests
- `chore`: Maintenance tasks, dependency updates

### Examples

```bash
feat(upload): add drag and drop file upload
fix(sharing): resolve permission check on shared folders
docs(readme): update R2 configuration instructions
refactor(files): extract file operations into service class
```

## Testing

### Running Tests

```bash
# PHP Tests
php artisan test

# Or with PHPUnit directly
./vendor/bin/phpunit

# JavaScript Tests (if configured)
npm run test
```

### Writing Tests

- Write unit tests for business logic
- Write feature tests for endpoints
- Test edge cases and error scenarios
- Mock external services (R2, mail, etc.)

Example:
```php
public function test_user_can_upload_file(): void
{
    Storage::fake('r2');
    
    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('document.pdf', 100);
    
    $response = $this->actingAs($user)
        ->post('/files', ['file' => $file]);
    
    $response->assertStatus(201);
    Storage::disk('r2')->assertExists('path/to/file.pdf');
}
```

## Branch Naming

Use descriptive branch names:

- `feature/add-bulk-download`
- `fix/upload-timeout-issue`
- `refactor/file-service-cleanup`
- `docs/update-deployment-guide`

## Pull Request Process

1. **Update the README.md** with details of changes if applicable
2. **Update documentation** for any new features
3. **Add tests** for new functionality
4. **Ensure all tests pass** before submitting
5. **Update the CHANGELOG** (if exists) with notable changes
6. **Link relevant issues** in the PR description
7. **Request review** from maintainers

### PR Title Format

```
type(scope): Brief description

Example:
feat(sharing): Add public link sharing with expiration
```

### PR Description Template

```markdown
## Description
Brief description of what this PR does

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Changes Made
- List of changes

## Testing Done
- How you tested these changes

## Screenshots (if applicable)
Add screenshots here

## Related Issues
Fixes #123
Relates to #456
```

## Code Review Process

- All submissions require review from at least one maintainer
- Reviews will check for:
  - Code quality and standards compliance
  - Test coverage
  - Documentation updates
  - Performance implications
  - Security considerations

## Questions?

Feel free to open an issue with the `question` label or reach out to the maintainers.

## Recognition

Contributors will be recognized in:
- GitHub contributors list
- Release notes (for significant contributions)
- README acknowledgments section (for major features)

---

Thank you for contributing to HamzaVaultX! 🎉
