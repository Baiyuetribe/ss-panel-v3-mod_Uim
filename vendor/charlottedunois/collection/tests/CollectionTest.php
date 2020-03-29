<?php
/**
 * Collection
 * Copyright 2016-2018 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Collection/blob/master/LICENSE
**/

namespace CharlotteDunois\Collect\Tests;

class CollectionTest extends \PHPUnit\Framework\TestCase {
    function testCreateWithDataAndGetAll() {
        $arr = array(5, 5, 20);
        $c = new \CharlotteDunois\Collect\Collection($arr);
        $this->assertSame($arr, $c->all());
    }
    
    function testCountable() {
        $c = new \CharlotteDunois\Collect\Collection(array(5, 5, 20));
        $this->assertSame(3, \count($c));
    }
    
    function testIterator() {
        $arr = array(5, 5, 20);
        $c = new \CharlotteDunois\Collect\Collection($arr);
        
        $i = 0;
        foreach($c as $key => $val) {
            $this->assertSame($i, $key);
            $this->assertSame($arr[$i], $val);
            $i++;
        }
    }
    
    function testHas() {
        $c = new \CharlotteDunois\Collect\Collection();
        
        $this->assertFalse($c->has(0));
        $this->assertSame($c, $c->set(0, 500));
        $this->assertTrue($c->has(0));
    }
    
    function testGetSet() {
        $c = new \CharlotteDunois\Collect\Collection();
        
        $this->assertSame($c, $c->set(0, 500));
        $this->assertSame(500, $c->get(0));
        $this->assertNull($c->get(1));
    }
    
    function testDelete() {
        $c = new \CharlotteDunois\Collect\Collection();
        
        $this->assertSame($c, $c->set(0, 500));
        $this->assertTrue($c->has(0));
        
        $this->assertSame($c, $c->delete(0));
        $this->assertFalse($c->has(0));
    }
    
    function testClear() {
        $c = new \CharlotteDunois\Collect\Collection();
        
        $c->set(0, 500);
        $this->assertSame(array(500), $c->all());
        
        $c->clear();
        $this->assertSame(array(), $c->all());
    }
    
    function testChunk() {
        $arr = array(5, 5, 20);
        $c = new \CharlotteDunois\Collect\Collection($arr);
        
        $c2 = $c->chunk(2);
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotEquals($c, $c2);
        
        $this->assertSame(array(array(5, 5), array(20)), $c2->all());
    }
    
    function testCount() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42));
        $this->assertSame(2, $c->count());
    }
    
    function testCopy() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42));
        
        $c2 = $c->copy();
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotSame($c, $c2);
        
        $this->assertSame($c->all(), $c2->all());
    }
    
    function testDiff() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42));
        
        $diff = $c->diff(array(15));
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $diff);
        $this->assertNotEquals($c, $diff);
        
        $this->assertSame(array(1 => 42), $diff->all());
    }
    
    function testDiffCollection() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42));
        
        $diff = $c->diff((new \CharlotteDunois\Collect\Collection(array(15))));
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $diff);
        $this->assertNotEquals($c, $diff);
        
        $this->assertSame(array(1 => 42), $diff->all());
    }
    
    function testDiffKeys() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42));
        
        $diff = $c->diffKeys((new \CharlotteDunois\Collect\Collection(array(0 => 42))));
        
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $diff);
        $this->assertNotEquals($c, $diff);
        
        $this->assertSame(array(1 => 42), $diff->all());
    }
    
    function testDiffKeysCollection() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42));
        
        $diff = $c->diffKeys((new \CharlotteDunois\Collect\Collection(array(1 => 42))));
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $diff);
        $this->assertNotEquals($c, $diff);
        
        $this->assertSame(array(0 => 15), $diff->all());
    }
    
    function testEach() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42));
        
        $i = 0;
        $c->each(function ($value, $key) use (&$i) {
            if($i === 0) {
                $this->assertSame(15, $value);
                $this->assertSame(0, $key);
            } elseif($i === 1) {
                $this->assertSame(42, $value);
                $this->assertSame(1, $key);
            } else {
                throw new \RuntimeException('Unexpected invocation');
            }
            
            $i++;
        });
        
        $this->assertSame(2, $i);
    }
    
    function testEvery() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42));
        
        $this->assertTrue($c->every(function ($val, $key) {
            return ($val > 10 && $key < 2);
        }));
    }
    
    function testEveryFailure() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42));
        
        $this->assertFalse($c->every(function ($val, $key) {
            return ($val < 40 && $key < 2);
        }));
    }
    
    function testExcept() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 25, 50));
        
        $c2 = $c->except(array(0, 3));
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotEquals($c, $c2);
        
        $this->assertSame(array(1 => 42, 2 => 30, 4 => 50), $c2->all());
    }
    
    function testFilter() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 25, 50));
        
        $c2 = $c->filter(function ($value, $key) {
            return ($value < 40 || $key < 2);
        });
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotEquals($c, $c2);
        
        $this->assertSame(array(0 => 15, 1 => 42, 2 => 30, 3 => 25), $c2->all());
    }
    
    function testFirst() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 25, 50));
        $this->assertSame(15, $c->first());
        
        $c2 = new \CharlotteDunois\Collect\Collection();
        $this->assertNull($c2->first());
    }
    
    function testFirstCallback() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 25, 50));
        $this->assertSame(50, $c->first(function ($value, $key) {
            return ($value > 45 && $key < 5);
        }));
        
        $c2 = new \CharlotteDunois\Collect\Collection();
        $this->assertNull($c2->first(function ($value, $key) {
            return ($value > 60 || $key > 5);
        }));
    }
    
    function testFlatten() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, array(42, array(30, 52, array(50))), (new \CharlotteDunois\Collect\Collection(array(25, 50)))));
        
        $c2 = $c->flatten();
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotEquals($c, $c2);
        
        $this->assertSame(array(15, 42, 30, 52, 50, 25, 50), $c2->all());
    }
    
    function testFlattenDepth() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, array(42, array(30, 52, array(50))), (new \CharlotteDunois\Collect\Collection(array(25, 50)))));
        
        $c2 = $c->flatten(1);
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotEquals($c, $c2);
        
        $this->assertSame(array(15, 42, array(30, 52, array(50)), 25, 50), $c2->all());
    }
    
    function testGroupByEmptyColumn() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 25, 50));
        
        $this->assertSame($c, $c->groupBy(null));
        $this->assertSame($c, $c->groupBy(''));
    }
    
    function testGroupByCallback() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 25, 50));
        
        $c2 = $c->groupBy(function ($value, $key) {
            return ((int) ($value > 27 || $key > 5));
        });
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotEquals($c, $c2);
        
        $this->assertSame(array(array(15, 25), array(42, 30, 50)), $c2->all());
    }
    
    function testGroupByArray() {
        $val = array(
            array(
                'key2' => 0,
                'val' => 15
            ),
            array(
                'key2' => 1,
                'val' => 42
            ),
            array(
                'key2' => 1,
                'val' => 30
            ),
            array(
                'key2' => 0,
                'val' => 25
            ),
            array(
                'key2' => 1,
                'val' => 50
            )
        );
        
        $c = new \CharlotteDunois\Collect\Collection($val);
        
        $c2 = $c->groupBy('key2');
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotEquals($c, $c2);
        
        $target = array(
            array(
                array(
                    'key2' => 0,
                    'val' => 15
                ),
                array(
                    'key2' => 0,
                    'val' => 25
                )
            ),
            array(
                array(
                    'key2' => 1,
                    'val' => 42
                ),
                array(
                    'key2' => 1,
                    'val' => 30
                ),
                array(
                    'key2' => 1,
                    'val' => 50
                )
            )
        );
        
        $this->assertSame($target, $c2->all());
    }
    
    function testGroupByObject() {
        $val = array(
            ((object) array(
                'key2' => 0,
                'val' => 15
            )),
            ((object) array(
                'key2' => 1,
                'val' => 42
            )),
            ((object) array(
                'key2' => 1,
                'val' => 30
            )),
            ((object) array(
                'key2' => 0,
                'val' => 25
            )),
            ((object) array(
                'key2' => 1,
                'val' => 50
            ))
        );
        
        $c = new \CharlotteDunois\Collect\Collection($val);
        
        $c2 = $c->groupBy('key2');
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotEquals($c, $c2);
        
        $target = array(
            array(
                ((object) array(
                    'key2' => 0,
                    'val' => 15
                )),
                ((object) array(
                    'key2' => 0,
                    'val' => 25
                ))
            ),
            array(
                ((object) array(
                    'key2' => 1,
                    'val' => 42
                )),
                ((object) array(
                    'key2' => 1,
                    'val' => 30
                )),
                ((object) array(
                    'key2' => 1,
                    'val' => 50
                ))
            )
        );
        
        $this->assertEquals($target, $c2->all());
    }
    
    function testImplode() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30));
        
        $this->assertSame('15, 42, 30', $c->implode(null, ', '));
    }
    
    function testImplodeArray() {
        $val = array(
            array('k' => 15),
            array('k' => 42),
            array('k' => 30)
        );
        
        $c = new \CharlotteDunois\Collect\Collection($val);
        
        $this->assertSame('15, 42, 30', $c->implode('k', ', '));
    }
    
    function testImplodeArrayFailure() {
        $val = array(
            array('k' => 15),
            array('k' => 42),
            array('k' => 30)
        );
        
        $c = new \CharlotteDunois\Collect\Collection($val);
        
        $this->expectException(\BadMethodCallException::class);
        $c->implode('v', ', ');
    }
    
    function testImplodeObject() {
        $val = array(
            ((object) array('k' => 15)),
            ((object) array('k' => 42)),
            ((object) array('k' => 30))
        );
        
        $c = new \CharlotteDunois\Collect\Collection($val);
        
        $this->assertSame('15, 42, 30', $c->implode('k', ', '));
    }
    
    function testImplodeObjectFailure() {
        $val = array(
            ((object) array('k' => 15)),
            ((object) array('k' => 42)),
            ((object) array('k' => 30))
        );
        
        $c = new \CharlotteDunois\Collect\Collection($val);
        
        $this->expectException(\BadMethodCallException::class);
        $c->implode('v', ', ');
    }
    
    function testIndexOf() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30));
        $this->assertSame(1, $c->indexOf(42));
    }
    
    function testIndexOfNull() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30));
        $this->assertNull($c->indexOf(50));
    }
    
    function testIntersect() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30));
        
        $c2 = $c->intersect(array(15, 30));
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotEquals($c, $c2);
        
        $this->assertSame(array(15, 2 => 30), $c2->all());
    }
    
    function testIntersectCollection() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30));
        
        $c2 = $c->intersect((new \CharlotteDunois\Collect\Collection(array(15, 42))));
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotEquals($c, $c2);
        
        $this->assertSame(array(15, 42), $c2->all());
    }
    
    function testKeys() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30));
        
        $c2 = $c->keys();
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotEquals($c, $c2);
        
        $this->assertSame(array(0, 1, 2), $c2->all());
    }
    
    function testLast() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 25, 50));
        $this->assertSame(50, $c->last());
        
        $c2 = new \CharlotteDunois\Collect\Collection();
        $this->assertNull($c2->last());
    }
    
    function testLastCallback() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 25, 50));
        $this->assertSame(25, $c->last(function ($value, $key) {
            return ($value < 45 && $key < 5);
        }));
        
        $c2 = new \CharlotteDunois\Collect\Collection();
        $this->assertNull($c2->last(function ($value, $key) {
            return ($value > 60 || $key > 5);
        }));
    }
    
    function testMap() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30));
        
        $c2 = $c->map(function ($value, $key) {
            return (($value * 2) + $key);
        });
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotEquals($c, $c2);
        
        $this->assertSame(array(30, 85, 62), $c2->all());
    }
    
    function testMax() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30));
        $this->assertSame(42, $c->max());
    }
    
    function testMaxKey() {
        $val = array(
            array('k' => 15),
            array('k' => 42),
            array('k' => 30)
        );
        
        $c = new \CharlotteDunois\Collect\Collection($val);
        $this->assertSame(42, $c->max('k'));
    }
    
    function testMin() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30));
        $this->assertSame(15, $c->min());
    }
    
    function testMinKey() {
        $val = array(
            array('k' => 15),
            array('k' => 42),
            array('k' => 30)
        );
        
        $c = new \CharlotteDunois\Collect\Collection($val);
        $this->assertSame(15, $c->min('k'));
    }
    
    function testMerge() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30));
        
        $c2 = $c->merge((new \CharlotteDunois\Collect\Collection(array(40, 0))));
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotEquals($c, $c2);
        
        $this->assertSame(array(15, 42, 30, 40, 0), $c2->all());
    }
    
    function testNth() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 40, 0));
        
        $c2 = $c->nth(2);
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotEquals($c, $c2);
        
        $this->assertSame(array(15, 30, 0), $c2->all());
    }
    
    function testNthOffset() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 40, 0));
        
        $c2 = $c->nth(2, 1);
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotEquals($c, $c2);
        
        $this->assertSame(array(42, 40), $c2->all());
    }
    
    function testNth0nth() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 40, 0));
        
        $this->expectException(\InvalidArgumentException::class);
        $c->nth(0);
    }
    
    function testNthMinusnth() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 40, 0));
        
        $this->expectException(\InvalidArgumentException::class);
        $c->nth(-1);
    }
    
    function testOnly() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 40, 0));
        
        $c2 = $c->only(array(0, 4));
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotEquals($c, $c2);
        
        $this->assertSame(array(0 => 15, 4 => 0), $c2->all());
    }
    
    function testPartition() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 40, 0));
        
        $ca = $c->partition(function ($value, $key) {
            return ($value > 30 || $key === -1);
        });
        $this->assertInternalType('array', $ca);
        $this->assertSame(2, \count($ca));
        
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $ca[0]);
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $ca[1]);
        
        $this->assertSame(array(1 => 42, 3 => 40), $ca[0]->all());
        $this->assertSame(array(0 => 15, 2 => 30, 4 => 0), $ca[1]->all());
    }
    
    function testPluck() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 40, 0));
        
        $c2 = $c->pluck('k');
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotEquals($c, $c2);
        
        $this->assertSame(array(), $c2->all());
    }
    
    function testPluckArray() {
        $val = array(
            array(
                'key2' => 5,
                'val' => 15
            ),
            array(
                'key2' => 2,
                'val' => 42
            ),
            array(
                'key2' => 10,
                'val' => 30
            ),
            array(
                'key2' => 12,
                'val' => 25
            ),
            array(
                'key2' => 42,
                'val' => 50
            )
        );
        
        $c = new \CharlotteDunois\Collect\Collection($val);
        
        $c2 = $c->pluck('val', 'key2');
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotEquals($c, $c2);
        
        $target = array(
            5 => 15,
            2 => 42,
            10 => 30,
            12 => 25,
            42 => 50
        );
        
        $this->assertSame($target, $c2->all());
    }
    
    function testPluckObject() {
        $val = array(
            ((object) array(
                'key2' => 5,
                'val' => 15
            )),
            ((object) array(
                'key2' => 2,
                'val' => 42
            )),
            ((object) array(
                'key2' => 10,
                'val' => 30
            )),
            ((object) array(
                'key2' => 12,
                'val' => 25
            )),
            ((object) array(
                'key2' => 42,
                'val' => 50
            ))
        );
        
        $c = new \CharlotteDunois\Collect\Collection($val);
        
        $c2 = $c->pluck('val', 'key2');
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotEquals($c, $c2);
        
        $target = array(
            5 => 15,
            2 => 42,
            10 => 30,
            12 => 25,
            42 => 50
        );
        
        $this->assertSame($target, $c2->all());
    }
    
    function testRandom() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 40, 0));
        
        $c2 = $c->random(1);
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotEquals($c, $c2);
        
        $this->assertSame(1, $c2->count());
    }
    
    function testRandomMultiple() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 40, 0));
        
        $c2 = $c->random(3);
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotEquals($c, $c2);
        
        $this->assertSame(3, $c2->count());
    }
    
    function testReduce() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 40, 0));
        
        $carry = $c->reduce(function ($carry, $val) {
            return ($carry + $val);
        }, 5);
        
        $this->assertSame(132, $carry);
    }
    
    function testReverse() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 40, 0));
        
        $c2 = $c->reverse();
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotEquals($c, $c2);
        
        $this->assertSame(array(0, 40, 30, 42, 15), $c2->all());
    }
    
    function testReversePreserve() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 40, 0));
        
        $c2 = $c->reverse(true);
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotSame($c, $c2);
        
        $this->assertSame(array(4 => 0, 3 => 40, 2 => 30, 1 => 42, 0 => 15), $c2->all());
    }
    
    function testSearch() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 40, 0));
        
        $this->assertSame(2, $c->search(30, false));
    }
    
    function testSearchStrict() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 40, 0));
        
        $this->assertFalse($c->search('15', true));
    }
    
    function testShuffle() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 40, 0));
        
        $c2 = $c->shuffle();
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotEquals($c, $c2);
    }
    
    function testSlice() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 40, 0));
        
        $c2 = $c->slice(1, 2);
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotEquals($c, $c2);
        
        $this->assertSame(array(42, 30), $c2->all());
    }
    
    function testSlicePreserve() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 40, 0));
        
        $c2 = $c->slice(1, 2, true);
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotEquals($c, $c2);
        
        $this->assertSame(array(1 => 42, 2 => 30), $c2->all());
    }
    
    function testSome() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 40, 0));
        
        $this->assertTrue($c->some(function ($value, $key) {
            return ($value > 20 && $key > 2);
        }));
    }
    
    function testSomeFailure() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 40, 0));
        
        $this->assertFalse($c->some(function ($value, $key) {
            return ($value > 50 && $key === 0);
        }));
    }
    
    function testSort() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 40, 0));
        
        $c2 = $c->sort();
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotSame($c, $c2);
        
        $this->assertSame(array(4 => 0, 0 => 15, 2 => 30, 3 => 40, 1 => 42), $c2->all());
    }
    
    function testSortDescending() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 40, 0));
        
        $c2 = $c->sort(true);
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotSame($c, $c2);
        
        $this->assertSame(array(1 => 42, 3 => 40, 2 => 30, 0 => 15, 4 => 0), $c2->all());
    }
    
    function testSortKey() {
        $c = new \CharlotteDunois\Collect\Collection(array(4 => 0, 0 => 15, 2 => 30, 3 => 40, 1 => 42));
        
        $c2 = $c->sortKey();
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotSame($c, $c2);
        
        $this->assertSame(array(15, 42, 30, 40, 0), $c2->all());
    }
    
    function testSortKeyDescending() {
        $c = new \CharlotteDunois\Collect\Collection(array(4 => 0, 0 => 15, 2 => 30, 3 => 40, 1 => 42));
        
        $c2 = $c->sortKey(true);
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotSame($c, $c2);
        
        $this->assertSame(array(4 => 0, 3 => 40, 2 => 30, 1 => 42, 0 => 15), $c2->all());
    }
    
    function testSortCustom() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 40, 0));
        
        $c2 = $c->sortCustom(function ($a, $b) {
            return ($b <=> $a);
        });
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotSame($c, $c2);
        
        $this->assertSame(array(1 => 42, 3 => 40, 2 => 30, 0 => 15, 4 => 0), $c2->all());
    }
    
    function testSortCustomKey() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 40, 0));
        
        $c2 = $c->sortCustomKey(function ($a, $b) {
            return ($b <=> $a);
        });
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotSame($c, $c2);
        
        $this->assertSame(array(4 => 0, 3 => 40, 2 => 30, 1 => 42,  0 => 15), $c2->all());
    }
    
    function testUnique() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 40, 0, 40, 42, 30, 0, 1));
        
        $c2 = $c->unique(null);
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        
        $this->assertNotEquals($c, $c2);
    }
    
    function testUniqueWithKeyOnScalar() {
        $c = new \CharlotteDunois\Collect\Collection(array(15, 42, 30, 40, 0, 40, 42, 30, 0, 1));
        
        $c2 = $c->unique('k');
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        
        $this->assertNotEquals($c, $c2);
    }
    
    function testUniqueKeyArray() {
        $val = array(
            array('k' => 15),
            array('k' => 42),
            array('k' => 30),
            array('k' => 30),
            array('k' => 15)
        );
        
        $c = new \CharlotteDunois\Collect\Collection($val);
        
        $c2 = $c->unique('k');
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotEquals($c, $c2);
        
        $target = array(
            array('k' => 15),
            array('k' => 42),
            array('k' => 30)
        );
        
        $this->assertSame($target, $c2->all());
    }
    
    function testUniqueKeyArrayFailure() {
        $val = array(
            array(15),
            array(42)
        );
        
        $c = new \CharlotteDunois\Collect\Collection($val);
        
        $this->expectException(\BadMethodCallException::class);
        $c->unique('val');
    }
    
    function testUniqueKeyObject() {
        $val = array(
            ((object) array('k' => 15)),
            ((object) array('k' => 42)),
            ((object) array('k' => 30)),
            ((object) array('k' => 30)),
            ((object) array('k' => 15))
        );
        
        $c = new \CharlotteDunois\Collect\Collection($val);
        
        $c2 = $c->unique('k');
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotEquals($c, $c2);
        
        $target = array(
            ((object) array('k' => 15)),
            ((object) array('k' => 42)),
            ((object) array('k' => 30))
        );
        
        $this->assertEquals($target, $c2->all());
    }
    
    function testUniqueKeyObjectFailure() {
        $val = array(
            ((object) array(15)),
            ((object) array(42))
        );
        
        $c = new \CharlotteDunois\Collect\Collection($val);
        
        $this->expectException(\BadMethodCallException::class);
        $c->unique('val');
    }
    
    function testValues() {
        $arr = array(5 => 15, 4 => 42, 30, 0 => 40, 'a' => 0, 40, 42, 30, 0, 1);
        $c = new \CharlotteDunois\Collect\Collection($arr);
        
        $c2 = $c->values();
        $this->assertInstanceOf(\CharlotteDunois\Collect\Collection::class, $c2);
        $this->assertNotEquals($c, $c2);
        
        $this->assertSame(\array_values($arr), $c2->all());
    }
}
