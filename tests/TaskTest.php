<?php

namespace TodoTxt\Tests;

use TodoTxt\Task;

class TaskTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test simple tasks, whitespace trimming and a few edge cases
     */
    public function testStandard()
    {
        $task = new Task("This is a task");
        $this->assertEquals((string) $task, "This is a task");
    }
        
    public function testTrailingWhitespace()
    {
        // Trailing whitespace of all kinds
        $task = new Task("This is  a task  \n\r\t");
        $this->assertEquals((string) $task, "This is  a task");
    }
    
    public function testEmptyTask()
    {
        // Empty string and purely whitespace
        $this->setExpectedException('TodoTxt\Exceptions\EmptyStringException');
        $task = new Task('');
    }
    
    public function testWhitespace()
    {
        $this->setExpectedException('TodoTxt\Exceptions\EmptyStringException');
        $task = new Task('  ');
    }
    
    public function testControlWhitespace()
    {
        $this->setExpectedException('TodoTxt\Exceptions\EmptyStringException');
        $task = new Task("\r\n\t");
    }
    
    public function testValidCompleted()
    {
        // Valid completion markers
        $task = new Task('x 2011-09-11 Completed task');
        $this->assertTrue($task->isComplete());
        $this->assertInstanceOf('DateTime', $task->getCompletionDate());
        $this->assertEquals($task->getCompletionDate()->format('Y-m-d'), '2011-09-11');
    }
    
    public function testUppercaseCompletedMarker()
    {
        // Test uppercase X
        $task = new Task('X 2011-09-11 Completed task');
        $this->assertTrue($task->isComplete());
    }
    
    public function testInvalidCompletionMarker()
    {
        // Test marker not at start
        $task = new Task('Hello x 2011-08-03 Incomplete task');
        $this->assertFalse($task->isComplete());
    }
    
    public function testMissingDate()
    {
        // Missing date
        $task = new Task('x Completed task');
        $this->assertFalse($task->isComplete());
        $this->assertNull($task->getCompletionDate());
    }
    
    public function testInvalidCompletionDateUnmatched()
    {
        // Invalid date not matched by regex
        $task = new Task('x 20111-09-11 Completed task');
        $this->assertFalse($task->isComplete());
        $this->assertNull($task->getCompletionDate());
    }
    
    public function testInvalidCompletionDateMatched()
    {
        // Invalid date, matched by regex, DateTime exception caught
        $task = new Task('x 2011-09-50 Completed task');
        $this->assertFalse($task->isComplete());
        $this->assertNull($task->getCompletionDate());
    }
    
    public function testNoBodyWhitespace()
    {
        // Even with whitespace, a task with no body should not have
        // a creation date
        $task = new Task('2011-08-03 ');
        $this->assertEquals($task->getTask(), '2011-08-03');
        $this->assertNull($task->getCreationDate());
    }
    
    public function testCompletedNoBodyWhitespace()
    {
        // Even with whitespace, a task with no body should not have
        // markers detected(?)
        $task = new Task('x 2011-08-03 ');
        $this->assertEquals($task->getTask(), 'x 2011-08-03');
        $this->assertNull($task->getCompletionDate());
    }
    
    public function testCreationDate()
    {
        // Leading date matched as creation date, rather than completion
        $task = new Task("2011-09-11 Incomplete task");
        $this->assertFalse($task->isComplete());
        $this->assertNull($task->getCompletionDate());
        $this->assertInstanceOf("DateTime", $task->getCreationDate());
        $this->assertEquals($task->getCreationDate()->format("Y-m-d"), "2011-09-11");
    }
    
    public function testInvalidCreationDateUnmatched()
    {
        // An invalid date, unmatched by regex
        $task = new Task("20111-09-01 Something");
        $this->assertFalse($task->isComplete());
        $this->assertNull($task->getCompletionDate());
    }
    
    public function testInvalidCreationDateMatched()
    {
        // An invalid date, unmatched by regex, DateTime exception caught
        $task = new Task("2011-09-50 Something");
        $this->assertFalse($task->isComplete());
        $this->assertNull($task->getCompletionDate());
    }
    
    public function testValidPriority()
    {
        $task = new Task("(A) Important task");
        $this->assertEquals($task->getPriority(), "A");
    }
    
    public function testMulticharPriority()
    {
        $task = new Task("(AA) Important task");
        $this->assertNull($task->getPriority());
    }
    
    public function testLowercasePriority()
    {
        $task = new Task("(a) Important task");
        $this->assertNull($task->getPriority());
    }

    public function testValidProject() {
        $task = new Task("Push to +todo.txt-web");
        $this->assertInstanceOf("TodoTxt\Project", $task->projects[0]);
        $this->assertCount(1, $task->projects);
        $this->assertTrue("todo.txt-web" == $task->projects[0]->project);
    }
    
    public function testValidProjects()
    {
        $task = new Task("Push to +todo.txt-web +open-source");
        $this->assertCount(2, $task->projects);
        $this->assertTrue("todo.txt-web" == $task->projects[0]->project);
        $this->assertTrue("open-source" == $task->projects[1]->project);
    }
    
    public function testInvalidProject()
    {
        $task = new Task("Update +todo* today.");
        $this->assertTrue(count($task->projects) == 0);
    }
    
    public function testValidContext()
    {
        $task = new Task("Update @todotxt.net");
        $this->assertInstanceOf("TodoTxt\Context", $task->contexts[0]);
        $this->assertCount(1, $task->contexts);        
        $this->assertTrue("todotxt.net" == $task->contexts[0]->context);
    }
    
    public function testValidContexts()
    {
        $task = new Task("Update @todotxt.net @github");
        $this->assertCount(2, $task->contexts);
        $this->assertTrue("todotxt.net" == $task->contexts[0]->context);
        $this->assertTrue("github" == $task->contexts[1]->context);
    }
    
    public function testInvalidContext()
    {
        $task = new Task("Update @todo* today.");
        $this->assertTrue(count($task->contexts) == 0);
    }

    
    public function testValidMetadata()
    {
        $task = new Task("Essay due:today");
        $this->assertInstanceOf("TodoTxt\MetaData", $task->metadata[0]);
        $this->assertCount(1, $task->metadata);
        $this->assertEquals($task->due, "today");
    }
    
    public function testValidMultiMetadata()
    {
        $task = new Task("Hello due:today when:tomorrow");
        $this->assertCount(2, $task->metadata);
        $this->assertEquals($task->due, "today");
        $this->assertEquals($task->when, "tomorrow");
        $this->assertTrue($task->isDue());
    }
    
    public function testInvalidMetadataKey()
    {
        // Test that text preceding metadata needs to be whitespace or
        // start of string (i.e. not "-").
        $task = new Task("Important essay was-due:yesterday");
        $this->assertFalse(isset($task->due));
    }

    public function testValidDue()
    {
        $task = new Task('Task with a duedate due:2015-11-20');
        $this->assertTrue($task->isDue());
        $this->assertInstanceOf("DateTime", $task->getDueDate());
    }
    
    public function testValidAgeCompleted()
    {
        // Test a completed task
        $task = new Task('x 2011-09-10 2011-09-08 Run the latest tests');
        $this->assertInstanceOf('DateInterval', $task->age());
        $this->assertEquals($task->age()->days, 2);
    }
        
    public function testValidAgeToday()
    {
        // Test an uncompleted task created 2 days ago
        $today = new \DateTime("now");
        $past = $today->sub(new \DateInterval("P2D"));
        $task = new Task(sprintf("%s Run the latest tests", $past->format("Y-m-d")));
        $this->assertInstanceOf("DateInterval", $task->age());
        $this->assertEquals($task->age()->days, 2);
    }
    
    public function testAgeNoCreationDate()
    {
        // Test a task with no creation date
        $task = new Task("Try to remember what I have forgotten");
        $this->setExpectedException("TodoTxt\Exceptions\CannotCalculateAgeException");
        $task->age();
    }
    
    public function testAgeEarlierCompletionDate()
    {
        // Test a task with a completion date earlier than its creation date
        $task = new Task('x 2011-09-10 2011-09-13 Find new power source for the Delorean');
        $this->setExpectedException('TodoTxt\Exceptions\CompletionParadoxException');
        $task->age();
    }
    
    public function testMaximumValid()
    {
        // The maximum valid?
        $task = new Task("x 2011-09-11 2011-09-08 Review Tim's pull-request in +todo.txt-web on @github due:2011-09-12");
        $this->assertTrue($task->isComplete());
        $this->assertEquals($task->getCompletionDate()->format("Y-m-d"), "2011-09-11");
        $this->assertNull($task->getPriority());
        $this->assertEquals($task->getCreationDate()->format("Y-m-d"), "2011-09-08");
        $this->assertTrue($task->projects[0]->project == "todo.txt-web");
        $this->assertTrue($task->contexts[0]->context == "github");
        $this->assertTrue($task->isDue());
        $this->assertEquals($task->due, "2011-09-12"); // @todo: plugins
    }

    public function testCompleteTask()
    {
        $task = new Task("complete this task");
        $task->complete();
        $this->assertTrue($task->isComplete());
        $now = new \DateTime("now");
        $this->assertEquals($task->getCompletionDate()->format("Y-m-d"), $now->format("Y-m-d"));
    }

    public function testUncompleteTask()
    {
        $task = new Task("x 2015-11-20 a completed task");
        $task->uncomplete();
        $this->assertFalse($task->isComplete());
        $this->assertNull($task->getCompletionDate());
        $this->assertEquals((string) $task, "a completed task");
    }

    public function testSetPriority()
    {
        $task = new Task('A Task needs a priority.');
        $task->setPriority('A');
        $this->assertEquals($task->getPriority(), 'A');
    }

    public function testInvalidPriority()
    {
        $this->setExpectedException('TodoTxt\Exceptions\InvalidStringException');
        $task = new Task('A Task needs a priority.');
        $task->setPriority('1');
    }

    public function testInvalidEmptyPriority()
    {
        $this->setExpectedException('TodoTxt\Exceptions\InvalidStringException');
        $task = new Task('A Task needs a priority.');
        $task->setPriority('');
    }

    public function testUnsetPriority()
    {
        $task = new Task('(A) This task has a priority.');
        $task->unsetPriority();
        $this->assertNull($task->getPriority());
    }

    public function testIncreasePriority()
    {
        $task = new Task('(B) This task has a priority.');
        $task->increasePriority();
        $this->assertEquals($task->getPriority(), 'A');
    }

    public function testDecreasePriority()
    {
        $task = new Task('(A) This task has a priority.');
        $task->decreasePriority();
        $this->assertEquals($task->getPriority(), 'B');
    }
}
