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
        $this->assertEquals($task->getPrio(), "A");
    }

    public function testMulticharPriority()
    {
        $task = new Task("(AA) Important task");
        $this->assertNull($task->getPrio());
    }

    public function testLowercasePriority()
    {
        $task = new Task("(a) Important task");
        $this->assertNull($task->getPrio());
    }

    public function testValidProject() {
        $task = new Task("Push to +todo.txt-web");
        $this->assertInstanceOf("TodoTxt\Project", $task->projects[0]);
        $this->assertCount(1, $task->projects);
        $this->assertTrue("todo.txt-web" == $task->projects[0]->project);
    }

    public function testMultipleValidProjects()
    {
        $task = new Task("Push to +todo.txt-web +open-source");
        $this->assertCount(2, $task->projects);
        $this->assertTrue("todo.txt-web" == $task->projects[0]->project);
        $this->assertTrue("open-source" == $task->projects[1]->project);
    }

    public function testInvalidProject()
    {
        $task = new Task("Update +todo* today.");
        $this->assertEMpty($task->projects);
    }

    public function testValidContext()
    {
        $task = new Task("Update @todotxt.net");
        $this->assertInstanceOf("TodoTxt\Context", $task->contexts[0]);
        $this->assertCount(1, $task->contexts);
        $this->assertTrue("todotxt.net" == $task->contexts[0]->context);
    }

    public function testMultipleValidContexts()
    {
        $task = new Task("Update @todotxt.net @github");
        $this->assertCount(2, $task->contexts);
        $this->assertTrue("todotxt.net" == $task->contexts[0]->context);
        $this->assertTrue("github" == $task->contexts[1]->context);
    }

    public function testInvalidContext()
    {
        $task = new Task("Update @todo* today.");
        $this->assertEmpty($task->contexts);
    }


    public function testValidMetadata()
    {
        $task = new Task("Essay due:today");
        $this->assertInstanceOf("TodoTxt\MetaData", $task->metadata[0]);
        $this->assertCount(1, $task->metadata);
        $this->assertEquals($task->metadata[0]->key, "due");
        $this->assertEquals($task->metadata[0]->value, "today");
    }

    public function testMultipleValidMetadata()
    {
        $task = new Task("Hello due:today when:tomorrow");
        $this->assertCount(2, $task->metadata);
        $this->assertEquals($task->metadata[0]->key, "due");
        $this->assertEquals($task->metadata[0]->value, "today");
        $this->assertEquals($task->metadata[1]->key, "when");
        $this->assertEquals($task->metadata[1]->value, "tomorrow");
    }

    public function testInvalidMetadataKey()
    {
        // Test that text preceding metadata needs to be whitespace or
        // start of string (i.e. not "-").
        $task = new Task("Important essay was-due:yesterday");
        $this->assertEmpty($task->metadata);
        $this->assertFalse($task->isDue());
    }

    public function testValidDue()
    {
        $task = new Task('Task with a duedate due:2015-11-20');
        $this->assertTrue($task->isDue());
        $this->assertInstanceOf("DateTime", $task->getDueDate());
        $this->assertEquals('2015-11-20', $task->getDueDate()->format("Y-m-d"));
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
        $task = new Task("x 2011-09-11 2011-09-08 Review Tim's pull-request in +todo.txt-web on @github due:2011-09-12 meta:data");
        $this->assertTrue($task->isComplete());
        $this->assertEquals($task->getCompletionDate()->format("Y-m-d"), "2011-09-11");
        $this->assertNull($task->getPrio());
        $this->assertEquals($task->getCreationDate()->format("Y-m-d"), "2011-09-08");
        $this->assertTrue($task->projects[0]->project == "todo.txt-web");
        $this->assertTrue($task->contexts[0]->context == "github");
        $this->assertTrue($task->isDue());
        $this->assertEquals($task->getDueDate()->format('Y-m-d'), "2011-09-12");
        $this->assertTrue($task->metadata[1]->key == "meta");
        $this->assertTrue($task->metadata[1]->value == "data");
    }

    /** Testing completion of Task */
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

    /** Testing manipulation of Priority */
    public function testHasPriority()
    {
        $task = new Task("(A) Important task");
        $this->assertTrue($task->hasPrio());
    }

    public function testHasNoPriority()
    {
        $task = new Task("Unimportant task");
        $this->assertFalse($task->hasPrio());
    }

    public function testSetPriority()
    {
        $task = new Task('A Task needs a priority.');
        $task->setPrio('A');
        $this->assertTrue($task->hasPrio());
        $this->assertEquals($task->getPrio(), 'A');
    }

    public function testInvalidPriorityLowerCase()
    {
        $this->setExpectedException('TodoTxt\Exceptions\InvalidStringException');
        $task = new Task('A Task needs a priority.');
        $task->setPrio('a');
    }

    public function testInvalidPriorityNumeric()
    {
        $this->setExpectedException('TodoTxt\Exceptions\InvalidStringException');
        $task = new Task('A Task needs a priority.');
        $task->setPrio('1');
    }

    public function testInvalidEmptyPriority()
    {
        $this->setExpectedException('TodoTxt\Exceptions\InvalidStringException');
        $task = new Task('A Task needs a priority.');
        $task->setPrio('');
    }

    public function testUnsetPriority()
    {
        $task = new Task('(A) This task has a priority.');
        $task->unsetPrio();
        $this->assertFalse($task->hasPrio());
        $this->assertNull($task->getPrio());
    }

    public function testIncreasePriority()
    {
        $task = new Task('(B) This task has a priority.');
        $task->increasePrio();
        $this->assertEquals($task->getPrio(), 'A');
    }

    public function testIncreasePriorityMultiple()
    {
        $task = new Task('(D) This task has a priority.');
        $task->increasePrio(3);
        $this->assertEquals($task->getPrio(), 'A');
    }

    public function testDecreasePriority()
    {
        $task = new Task('(A) This task has a priority.');
        $task->decreasePrio();
        $this->assertEquals($task->getPrio(), 'B');
    }

    public function testDecreasePriorityMultiple()
    {
        $task = new Task('(A) This task has a priority.');
        $task->decreasePrio(3);
        $this->assertEquals($task->getPrio(), 'D');
    }

    /** Testing editing of task */
    public function testEditTask()
    {
        $task = new Task('This is a task.');
        $task->edit('A complete new task.');
        $this->assertEquals((string) $task, "A complete new task.");
    }

    public function testAppendingString()
    {
        $task = new Task('This is a task');
        $task->append(' with appended text.');
        $this->assertEquals((string) $task, "This is a task with appended text.");
    }

    public function testPrependingString()
    {
        $task = new Task('This is a task.');
        $task->prepend('Prepended text to ');
        $this->assertEquals((string) $task, "Prepended text to This is a task.");
    }
}
