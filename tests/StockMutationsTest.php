<?php

namespace Mendela92\Stock\Tests;

class StockMutationsTest extends TestCase
{
    /** @test */
    public function it_can_have_no_mutations()
    {
        $this->assertEmpty($this->stockModel->stockMutations->toArray());
    }

    /** @test */
    public function it_can_have_some_mutations()
    {
        $this->stockModel->increaseStock(10);
        $this->stockModel->increaseStock(1);
        $this->stockModel->decreaseStock(1);

        $mutations = $this->stockModel->stockMutations->pluck(['amount'])->toArray();

        $this->assertEquals(['10', '1', '-1'], $mutations);
    }

    /** @test */
    public function it_has_positive_mutations_after_setting_stock()
    {
        $this->stockModel->increaseStock(5);
        $this->stockModel->setStock(10);

        $mutations = $this->stockModel->stockMutations->pluck(['amount'])->toArray();

        $this->assertEquals(['5', '5'], $mutations);
    }

    /** @test */
    public function it_has_mixed_mutations_after_setting_stock()
    {
        $this->stockModel->clearStock(10);
        $this->stockModel->setStock(5);

        $mutations = $this->stockModel->stockMutations->pluck(['amount'])->toArray();

        $this->assertEquals(['10', '-5'], $mutations);
    }

    /** @test */
    public function it_can_have_mutations_with_details()
    {
        $this->stockModel->increaseStock(10, [
            'details' => 'Test',
        ]);

        $mutations = $this->stockModel->stockMutations->pluck(['details'])->toArray();

        $this->assertEquals(['Test'], $mutations);
    }
}
