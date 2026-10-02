<?php

namespace Tests\Unit;

use App\Services\ModelKey;
use PHPUnit\Framework\TestCase;

class ModelKeyTest extends TestCase
{
    public function test_returns_null_for_missing_model(): void
    {
        $this->assertNull(ModelKey::get(null));
    }

    public function test_prefers_mongo_identifier(): void
    {
        $model = (object) ['_id' => 'abc123', 'id' => 99];

        $this->assertSame('abc123', ModelKey::get($model));
    }

    public function test_falls_back_to_standard_identifier(): void
    {
        $model = (object) ['id' => 99];

        $this->assertSame('99', ModelKey::get($model));
    }
}
