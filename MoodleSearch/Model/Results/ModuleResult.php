<?php

namespace MoodleSearch;

class ModuleResult extends Result
{
	public function icon()
	{
		global $OUTPUT;
		return trim($OUTPUT->pix_icon('icon', '', $this->tableName, array('class' => 'icon')));
	}

	public function url()
	{
		$resourceID = DataManager::getGlobalInstanceIDFromModuleInstanceID($this->tableName, $this->row->id);
		return new \moodle_url('/mod/' . $this->tableName . '/view.php', array('id' => $resourceID));
	}

	public function path()
	{
		//Get all info for the course this resource is in
		$course = DataManager::getCourse($this->row->course);

		$path = $this->getCategoryPath($course->category);

		if (function_exists('\course_get_icon')) {
			$courseIcon = \course_get_icon($course->id);
		} else {
			$courseIcon = false;
		}
		$path[] = array(
			'title' => 'Course',
			'name' => $course->fullname,
			'url' => new \moodle_url('/course/view.php', array('id' => $course->id)),
			'icon' => !empty($courseIcon) ? 'icon-'.$courseIcon : 'icon-archive'
		);

		//Get all info for the course section this resource is in
		$section = DataManager::getResourceSection($this->tableName, $this->row->id);
		if ($section->name) {
			$path[] = array(
				'title' => 'Section',
				'name' => $section->name,
				'url' => new \moodle_url('/course/view.php', array('id' => $course->id, 'sectionid' => $section->id)),
				'icon' => 'icon-th'
			);
		}
		return $path;
	}

	public function isVisible()
	{
		global $USER;

		$coursecontext = \context_course::instance($this->row->course);
		if (!is_enrolled($coursecontext, $USER)) {
			return 'notenrolled';
		}

		if (DataManager::canUserSeeModule($this->row->course, $this->tableName, $this->row->id)) {
			return true;
		} else {
			return 'notvisible';
		}
	}
}
