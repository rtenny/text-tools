# Image Alt Text Generator - Implementation Plan

## Overview

Add a fourth tab to the user tools interface that allows users to upload property images or provide image URLs, analyze them using Claude's Vision API, and generate SEO-optimized alt text descriptions in British English. The generated alt text will then be translated into German and Spanish.

---

## Feature Requirements

### User Interface
1. **Fourth tab** on the tools page: "Alt Text Generator"
2. **Input fields**:
   - Property Type (dropdown: Villa, Apartment, Finca, Townhouse, Penthouse)
   - Location/Town (dropdown: from assigned towns)
   - City (text input or dropdown)
   - Image source (toggle between upload and URL)
     - File upload (accept: jpg, jpeg, png, webp)
     - Image URL input field
3. **Output display**:
   - Preview of uploaded/loaded image
   - Three alt text options in British English (numbered 1-3)
   - Selection mechanism to choose preferred alt text
   - Translation boxes for German and Spanish (auto-translate selected option)
   - Copy buttons for each output

### Backend Functionality
1. **Image handling**:
   - Accept file uploads (max 5MB)
   - Accept image URLs (validate and fetch)
   - Convert images to base64 for Claude API
   - Validate image formats (JPEG, PNG, WebP)
2. **AI processing**:
   - Send image + context to Claude Vision API
   - Use provided prompt template with variable substitution
   - Parse 3 alt text options from response
   - Translate selected alt text to German and Spanish
3. **Validation**:
   - Required fields: property type, location, city, and image source
   - File size limits
   - Image format validation
   - URL format validation

### Translation
- Target languages: German (de), Spanish/European (es)
- Translate selected alt text option only
- Use existing translation service

---

## Technical Architecture

### Database Changes
**None required** - This is a stateless tool like the existing ones

### New Files to Create

#### 1. Controller
**File**: `public_html/app/Controllers/Tools/AltTextController.php`
- Method: `generateAltText()` - AJAX endpoint
  - Accept image file OR URL
  - Accept property_type, location, city
  - Process image (upload or fetch URL)
  - Convert to base64
  - Call ClaudeService vision method
  - Return 3 alt text options

#### 2. ClaudeService Enhancement
**File**: `public_html/app/Libraries/AIService/ClaudeService.php`
- New method: `generateImageAltText()`
  - Accept image base64, property_type, location, city
  - Build vision API request with prompt template
  - Handle image content in messages
  - Parse response (3 numbered options)
  - Return array of 3 alt text options

#### 3. AIServiceInterface Enhancement
**File**: `public_html/app/Libraries/AIService/AIServiceInterface.php`
- Add method signature: `generateImageAltText()`

### Files to Modify

#### 1. View - Add Fourth Tab
**File**: `public_html/app/Views/tools/index.php`
- Add "Alt Text" tab button (line ~9)
- Add tab content section with:
  - Form for image upload/URL and property details
  - Image preview area
  - Three alt text option outputs
  - Selection UI (radio buttons or click-to-select)
  - Translation boxes for de and es

#### 2. Routes
**File**: `public_html/app/Config/Routes.php`
- Add route: `$routes->post('alttext', 'Tools\AltTextController::generateAltText');`

#### 3. JavaScript
**File**: `public_html/public/js/app.js` (or create new `alttext.js`)
- Handle form submission
- Image upload/URL toggle
- Image preview
- AJAX call to backend
- Display 3 alt text options
- Handle alt text selection
- Trigger translations for selected option
- Copy functionality

#### 4. CSS (if needed)
**File**: `public_html/public/css/app.css`
- Image preview styling
- Alt text option selection UI
- Upload/URL toggle styles

---

## Implementation Phases

### Phase 1: Backend Foundation
**Goal**: Set up image processing and Claude Vision API integration

**Tasks**:
1. Create `AltTextController.php`
   - Scaffold basic controller structure
   - Add image upload handling
   - Add URL image fetching
   - Add image validation (format, size)
   - Add base64 conversion

2. Update `AIServiceInterface.php`
   - Add `generateImageAltText()` method signature

3. Update `ClaudeService.php`
   - Implement `generateImageAltText()` method
   - Build vision API request with image content
   - Implement prompt template with variable substitution:
     ```
     {TYPE} → property_type
     {CITY} → city
     {LOCATION} → location
     ```
   - Parse numbered list response (1-3)
   - Return array of alt text options

4. Add route in `Routes.php`
   - `POST /tools/alttext`

**Testing**:
- Test with sample images via Postman or curl
- Verify Claude Vision API responses
- Confirm 3 alt text options are returned correctly

---

### Phase 2: Frontend UI
**Goal**: Create user interface for the Alt Text Generator tab

**Tasks**:
1. Update `tools/index.php`
   - Add fourth tab button: "Alt Text Generator" with image icon
   - Create tab content section structure
   - Add form with fields:
     - Property Type dropdown (reuse existing options)
     - Location dropdown (from $towns)
     - City input field
     - Image source toggle (Upload / URL)
     - File input (hidden initially)
     - URL input field (hidden initially)
     - Image preview container
     - Generate button
   - Add output areas:
     - Three numbered alt text option boxes
     - Selection mechanism (radio buttons or clickable cards)
     - German translation box (with copy button)
     - Spanish translation box (with copy button)

2. Add styling for:
   - Image upload/URL toggle
   - Image preview (max width/height)
   - Alt text option cards (selectable)
   - Selected state highlighting

**Testing**:
- Verify tab switching works
- Check form layout and responsiveness
- Ensure all UI elements are visible and styled

---

### Phase 3: Frontend JavaScript Logic
**Goal**: Implement interactive behavior and API integration

**Tasks**:
1. Create JavaScript module for Alt Text Generator:
   - Tab initialization
   - Upload/URL toggle functionality
   - File input change handler:
     - Show image preview
     - Validate file type and size
     - Display file name
   - URL input change handler:
     - Load and preview image from URL
     - Validate URL format
   - Form submission:
     - Gather form data
     - Create FormData with file or URL
     - Show loading spinner
     - AJAX POST to `/tools/alttext`
     - Handle response
   - Display 3 alt text options
   - Alt text selection handler:
     - Mark selected option
     - Clear translation boxes
     - Trigger translation API calls for selected text
   - Translation handling:
     - AJAX call to existing `/tools/translate` endpoint
     - Show loading spinners
     - Display translated results
   - Copy button functionality
   - Error handling and display

2. Integrate with existing app.js structure
   - Follow existing patterns for AJAX, CSRF, error handling
   - Reuse existing translation functionality

**Testing**:
- Test image upload flow
- Test URL image flow
- Test form validation
- Test API integration
- Test alt text selection
- Test translation triggering
- Test copy functionality
- Test error scenarios

---

### Phase 4: Integration & Testing
**Goal**: End-to-end testing and refinement

**Tasks**:
1. Integration testing:
   - Test complete workflow: upload → generate → select → translate
   - Test complete workflow: URL → generate → select → translate
   - Test with various image types and sizes
   - Test with different property types and locations
   - Test error scenarios:
     - Invalid file format
     - File too large
     - Invalid URL
     - Network errors
     - API errors

2. Cross-browser testing:
   - Chrome, Firefox, Safari, Edge
   - Mobile responsiveness

3. Security review:
   - File upload validation
   - CSRF protection
   - XSS prevention
   - File size limits
   - URL validation (prevent SSRF)

4. Performance optimization:
   - Image size/quality optimization before upload
   - Lazy loading for image previews
   - API response caching (if applicable)

5. User experience refinement:
   - Loading states and spinners
   - Error messages clarity
   - Success feedback
   - Smooth transitions

**Testing Checklist**:
- [ ] Upload JPEG image → 3 alt texts generated
- [ ] Upload PNG image → 3 alt texts generated
- [ ] Upload WebP image → 3 alt texts generated
- [ ] Provide image URL → 3 alt texts generated
- [ ] Select alt text option 1 → translations appear
- [ ] Select alt text option 2 → translations update
- [ ] Select alt text option 3 → translations update
- [ ] Copy English alt text
- [ ] Copy German translation
- [ ] Copy Spanish translation
- [ ] Upload oversized file → error shown
- [ ] Upload invalid format → error shown
- [ ] Provide invalid URL → error shown
- [ ] API error → user-friendly message shown
- [ ] Missing required fields → validation errors shown

---

## File Upload Configuration

### CodeIgniter Config
**File**: `public_html/app/Config/App.php` (verify settings)
- Max file size: 5MB (5242880 bytes)
- Allowed types: jpg, jpeg, png, webp

### Server Configuration
- Ensure PHP `upload_max_filesize` ≥ 5MB
- Ensure PHP `post_max_size` ≥ 6MB
- Ensure writable directory exists: `writable/uploads/temp/`

---

## Claude Vision API Integration

### API Request Format
```php
[
    'model' => 'claude-sonnet-4-5-20250929',
    'max_tokens' => 1024,
    'messages' => [
        [
            'role' => 'user',
            'content' => [
                [
                    'type' => 'image',
                    'source' => [
                        'type' => 'base64',
                        'media_type' => 'image/jpeg', // or image/png, image/webp
                        'data' => '<base64_encoded_image>'
                    ]
                ],
                [
                    'type' => 'text',
                    'text' => '<prompt with variables substituted>'
                ]
            ]
        ]
    ]
]
```

### Prompt Template
```
You are an SEO and accessibility expert specializing in real estate property images.

Analyze this property image and generate exactly 3 different alt text descriptions in British English (UK spelling and terminology).

Property Context:
- Property Type: {TYPE}
- Location: {CITY}, {LOCATION}, Spain

Requirements for each alt text:
- Maximum 150 characters
- Use British English (e.g., 'colour' not 'color', 'modernised' not 'modernized')
- Include property context for better SEO (property type and/or location when relevant)
- Describe what's actually visible in the image (room type, features, views, style)
- Be specific and descriptive (avoid generic terms like 'nice' or 'beautiful')
- Focus on key visual elements that would interest potential buyers
- Natural, professional language

Examples of good alt text:
- 'Modern open-plan kitchen with sea views in Costa del Sol villa'
- 'Spacious master bedroom with fitted wardrobes in Marbella apartment'
- 'Sun-drenched terrace overlooking Mediterranean in luxury property'

Format your response as a numbered list from 1 to 3:
1. First alt text option
2. Second alt text option
3. Third alt text option

Provide ONLY the numbered list, no additional explanations.
```

### Response Parsing
Expected format:
```
1. <alt text option 1>
2. <alt text option 2>
3. <alt text option 3>
```

Parse using regex or string splitting to extract the 3 options.

---

## Security Considerations

### File Upload Security
1. **File type validation**: Verify MIME type and extension
2. **File size limits**: Max 5MB
3. **File name sanitization**: Remove/replace special characters
4. **Temporary storage**: Store in `writable/uploads/temp/` with unique names
5. **Cleanup**: Delete uploaded files after processing

### URL Image Fetching
1. **URL validation**: Verify format and protocol (http/https only)
2. **SSRF prevention**: Restrict to public IPs, block private/local IPs
3. **Size limits**: Limit downloaded file size
4. **Timeout**: Set reasonable timeout for fetching
5. **Content-Type validation**: Verify image MIME type from response headers

### General
1. **CSRF protection**: Use existing CSRF tokens
2. **Authentication**: Ensure routes are protected by auth filters
3. **Input sanitization**: Escape all user inputs
4. **Error messages**: Don't expose sensitive information

---

## Error Handling

### User-Facing Errors
- "Please upload an image or provide an image URL"
- "Invalid image format. Please upload JPG, PNG, or WebP"
- "Image file is too large. Maximum size is 5MB"
- "Invalid image URL. Please provide a valid URL"
- "Failed to load image from URL. Please check the URL and try again"
- "Failed to generate alt text. Please try again"
- "Translation failed. Please try again"

### Logging
- Log all API errors with details
- Log file upload failures
- Log URL fetching failures
- Include user_id and project_id in logs

---

## UI/UX Considerations

### Image Upload/URL Toggle
- Clear visual indication of which mode is active
- Smooth transition between modes
- Preserve form data when switching

### Image Preview
- Show thumbnail preview of uploaded/loaded image
- Display image dimensions and file size (if uploaded)
- Clear preview when image changes

### Alt Text Options Display
- Clear numbering (1, 2, 3)
- Selectable cards/boxes
- Visual feedback on selection (highlight, border, checkmark)
- Character count display for each option

### Loading States
- Spinner while generating alt text
- Spinner while translating
- Disable form during processing
- Clear loading state on error

### Responsive Design
- Mobile-friendly image upload
- Responsive grid for translations
- Touch-friendly selection mechanism

---

## Testing Strategy

### Unit Tests (Optional)
- Test image validation functions
- Test base64 conversion
- Test prompt template variable substitution
- Test response parsing

### Integration Tests
- Test complete upload → generate → translate flow
- Test complete URL → generate → translate flow
- Test error scenarios

### Manual Testing
- Test with various image types, sizes, and content
- Test with different property types and locations
- Test on different browsers and devices
- Test error handling and edge cases

---

## Rollout Plan

### Development (develop branch)
1. Complete Phase 1 (Backend Foundation)
2. Complete Phase 2 (Frontend UI)
3. Complete Phase 3 (Frontend JavaScript)
4. Complete Phase 4 (Integration & Testing)
5. Internal testing and bug fixes

### Staging/Testing
1. Deploy to staging environment
2. Comprehensive testing
3. User acceptance testing (UAT)
4. Performance testing

### Production (main branch)
1. Merge to main after approval
2. Deploy to production
3. Monitor logs and errors
4. Gather user feedback

---

## Future Enhancements (Out of Scope)

- Batch processing multiple images
- Save/history of generated alt texts
- Custom prompt templates per project
- AI-powered image cropping suggestions
- Alt text quality scoring
- Integration with image management systems
- Support for additional image formats (GIF, SVG, etc.)
- Alt text A/B testing suggestions

---

## Dependencies

### External Services
- Anthropic Claude API (Vision capabilities)
- Existing translation service (for German and Spanish)

### PHP Extensions
- cURL (for API calls and URL fetching)
- GD or Imagick (for image validation and potential resizing)
- fileinfo (for MIME type detection)

### Libraries
- Existing CodeIgniter 4 framework
- Existing AIService implementation

---

## Estimated Development Time

- **Phase 1** (Backend Foundation): 4-6 hours
- **Phase 2** (Frontend UI): 3-4 hours
- **Phase 3** (Frontend JavaScript): 4-6 hours
- **Phase 4** (Integration & Testing): 4-6 hours

**Total**: 15-22 hours

---

## Success Criteria

- [x] Users can upload images or provide URLs
- [x] Users can input property context (type, location, city)
- [x] System generates 3 British English alt text options
- [x] Users can select preferred alt text
- [x] Selected alt text is translated to German and Spanish
- [x] All outputs have copy functionality
- [x] Proper error handling and user feedback
- [x] Responsive and accessible UI
- [x] Secure file handling
- [x] Performance meets expectations (< 10s for generation)

---

## Notes

- Follow existing code patterns and conventions
- Reuse existing translation functionality
- Maintain consistent UI/UX with other tools
- Ensure proper error handling and logging
- Keep security best practices in mind
- Test thoroughly before deployment
