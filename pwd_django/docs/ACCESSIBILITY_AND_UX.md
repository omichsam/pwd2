# Accessibility and User Experience

## Implemented controls

- A visible-on-focus skip link and stable `main` target support keyboard users.
- All interactive elements receive a high-visibility focus indicator.
- System reduced-motion preferences suppress non-essential animation.
- Dashboard navigation and notification controls expose names and expanded state.
- Registration and assessment booking provide linked, focusable error summaries.
- Registration returns users to the step containing the invalid field.
- Strong password requirements are consistent between registration and password changes.
- Assessment booking saves a non-clinical draft in browser session storage and
  restores it after a refresh. It is removed on submission or when the tab session ends.
- Submit controls prevent accidental repeated submission, while a database
  constraint guarantees at most one active assessment per applicant.
- Server-rendered pagination retains keyboard-operable navigation and does not
  require JavaScript.

## Manual WCAG 2.2 AA release gate

Automated regression tests do not replace assistive-technology review. Before
production, test registration, login, assessment booking, clinical review,
county approval, certificate printing, and verification with:

- keyboard only at 100% and 200% zoom;
- NVDA with Firefox or Chrome, and VoiceOver with Safari where available;
- Windows High Contrast and `prefers-reduced-motion`;
- narrow mobile viewport, landscape orientation, and reflow at 320 CSS pixels;
- invalid, interrupted, refreshed, repeated, and successfully completed forms.

Record issues with WCAG criterion, user impact, severity, owner, evidence, and
retest result. Critical blockers include keyboard traps, unnamed controls,
inaccessible errors, loss of entered data, or inability to complete a journey.
