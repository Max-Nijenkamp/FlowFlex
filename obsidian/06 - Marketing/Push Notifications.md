---
tags: [flowflex, domain/marketing, push, notifications, phase/5]
domain: Marketing
panel: marketing
color: "#DB2777"
status: planned
last_updated: 2026-05-08
---

# Push Notifications

Send browser and mobile push notifications to re-engage customers — even when they're not on your site. Works for web (PWA), iOS, and Android. No app required for web push. Replaces OneSignal + your email marketing platform's push bolt-on.

**Who uses it:** Marketing teams, ecommerce managers, product teams
**Filament Panel:** `marketing`
**Depends on:** Core, [[Contact & Company Management]], [[CMS & Website Builder]], Service Worker (web push)
**Phase:** 5

---

## Features

### Web Push Notifications

- Browser push via Web Push API (Chrome, Firefox, Edge, Safari 16.1+)
- Opt-in prompt: customisable timing (exit intent, after 30s, after 2nd page view) and text
- HTTPS required (standard)
- Icon, title, body, badge, action buttons (up to 2)
- Click action: open URL, dismiss, custom action
- Works on desktop and mobile browsers

### Mobile Push (App)

- Requires FlowFlex-branded or white-label mobile app (PWA or native wrapper)
- Firebase Cloud Messaging (FCM) for Android, APNs for iOS
- Rich push: image, title, body, action buttons
- Silent push: background data sync without visible notification

### Campaign Types

- **Broadcast**: send to all subscribers or a segment
- **Triggered**: send based on event (abandoned cart, price drop, back-in-stock, new blog post published)
- **Drip sequence**: onboarding welcome series, re-engagement series
- **Transactional**: order confirmed, shipping update (high-priority delivery lane)

### Segmentation

- Segments same as email/SMS: role, purchase history, location, custom attributes
- Push-specific: browser type, OS, last active date, opt-in date
- Behaviour targeting: visited /pricing but didn't sign up

### Personalisation

- Dynamic tokens: {{first_name}}, {{product_name}}, {{discount_code}}
- Recommended products: AI personalised product in push based on browse history
- Send-time optimisation: send at time each subscriber is most likely to engage

### Analytics

- Sent, delivered, opened, clicked, dismissed, conversion
- Revenue attributed: click → purchase within window
- A/B test: split 50/50 on title or body, winner auto-deployed after N hours
- Opt-out rate per campaign

### Opt-Out Management

- Unsubscribe from push in browser settings (browser-native)
- In-app opt-out link in notification or preference centre
- Preference centre: "only notify me about orders, not promotions"
- Suppression list: never send to unsubscribed contacts

---

## Database Tables (3)

### `marketing_push_subscriptions`
| Column | Type | Notes |
|---|---|---|
| `contact_id` | ulid FK nullable | null for anonymous |
| `endpoint` | text | push service URL |
| `keys` | json | p256dh + auth |
| `platform` | enum | `web`, `android`, `ios` |
| `browser` | string nullable | |
| `subscribed_at` | timestamp | |
| `unsubscribed_at` | timestamp nullable | |

### `marketing_push_campaigns`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `title` | string | |
| `body` | text | |
| `icon_file_id` | ulid FK nullable | |
| `image_file_id` | ulid FK nullable | |
| `click_url` | string nullable | |
| `action_buttons` | json nullable | [{label, url}] |
| `segment_id` | ulid FK nullable | |
| `scheduled_at` | timestamp nullable | |
| `sent_count` | integer default 0 | |
| `clicked_count` | integer default 0 | |
| `status` | enum | `draft`, `scheduled`, `sending`, `sent` |

### `marketing_push_sends`
| Column | Type | Notes |
|---|---|---|
| `campaign_id` | ulid FK | |
| `subscription_id` | ulid FK | |
| `status` | enum | `queued`, `delivered`, `clicked`, `failed`, `expired` |
| `sent_at` | timestamp nullable | |
| `clicked_at` | timestamp nullable | |

---

## Permissions

```
marketing.push.view
marketing.push.create
marketing.push.send
marketing.push.manage-subscriptions
marketing.push.view-analytics
```

---

## Competitor Comparison

| Feature | FlowFlex | OneSignal | Klaviyo Push | Brevo |
|---|---|---|---|---|
| No separate subscription | ✅ | partial (free tier limited) | ❌ | partial |
| Web + mobile in one | ✅ | ✅ | ✅ | ✅ |
| Integrated with CRM | ✅ | ❌ | ✅ | ✅ |
| Send-time optimisation | ✅ | ✅ | ✅ | ❌ |
| AI product recommendations | ✅ | ❌ | partial | ❌ |
| Revenue attribution | ✅ | ✅ | ✅ | partial |

---

## Related

- [[Marketing Overview]]
- [[Email Marketing]]
- [[SMS & WhatsApp Marketing]]
- [[Contact & Company Management]]
- [[Ecommerce/Order Management]]
