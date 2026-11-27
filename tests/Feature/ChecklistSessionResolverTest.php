<?php

namespace Tests\Feature;

use App\Models\ChecklistSessionWindow;
use App\Services\Operational\ChecklistSessionResolver;

class ChecklistSessionResolverTest extends OperationalChecklistTestCase
{
    private ChecklistSessionResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = app(ChecklistSessionResolver::class);
    }

    public function test_active_session_uses_effective_window_for_outlet(): void
    {
        $this->travelTo(now()->setTime(13, 0));

        $restaurant = $this->makeRestaurant('209', '209 Dining');
        $this->makeAllDayWindow('lunch');

        $result = $this->resolver->activeSession($restaurant);

        $this->assertSame('lunch', $result['session_type']);
        $this->assertSame(now()->toDateString(), $result['business_date']);
    }

    public function test_outlet_override_takes_precedence_and_inactive_window_disables_session(): void
    {
        $restaurant = $this->makeRestaurant('XFH', 'Xiang Fu Hai');

        // Global aktif sepanjang hari; outlet override menjadi nonaktif.
        $this->makeAllDayWindow('lunch');
        ChecklistSessionWindow::create([
            'restaurant_id' => $restaurant->id,
            'session_type' => 'lunch',
            'start_time' => '12:00:00',
            'end_time' => '15:00:00',
            'is_active' => false,
        ]);

        $resolver = app(ChecklistSessionResolver::class);
        $windows = $resolver->windowsFor($restaurant);

        $this->assertArrayNotHasKey('lunch', $windows);
        $this->assertNull($resolver->activeSession($restaurant));
    }
}
