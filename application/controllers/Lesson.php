<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Lesson extends CI_Controller {


	public function __construct() {
        parent::__construct();
        $this->load->model('model', 'model');
    }


	public function get($pupil_id = null, $subject_id = null, $day = null, $time = null)
	{
		if($subject_id == null || $pupil_id == null || $day == null || $time == null) {
			header("HTTP/1.0 400 Bad Request");
			echo json_encode(array('Exception' => array('Message' => "Отсутствуют параметры")), JSON_UNESCAPED_UNICODE);
	    } else {

		    $day = date("Y-m-d", strtotime($day));
		    $lesson = $this->model->getLessonByDate($subject_id, $day, $time);
		    if(isset($lesson)) {

			    $homework = $lesson['LESSON_HOMEWORK'];
			    $status = $lesson['LESSON_STATUS'];
			    $lesson_id = $lesson['LESSON_ID'];
			    $lesson_date = $lesson['LESSON_DATE'];
			    $subject_id = $lesson['SUBJECTS_CLASS_ID'];
			    $theme = $lesson['LESSON_THEME'];
			    $time_id = $lesson['TIME_ID'];


			    $pass = $this->model->getPass($lesson_id, $pupil_id)['ATTENDANCE_PASS'];
				$note = $this->model->getNote($lesson_id, $pupil_id)['NOTE_TEXT'];

				$marks = $this->model->getMarks($lesson_id, $pupil_id);
				$arr = array();
				$i = 0;
				if(isset($marks)) {
					foreach($marks as $mark) {
						$arr[$i]["Ид"] = $mark['ACHIEVEMENT_ID'];
						$arr[$i]["Оценка"] = $mark['ACHIEVEMENT_MARK'];
						$arr[$i]["Тип"] = $mark['TYPE_NAME'];
						$arr[$i]["УрокИд"] = $mark['LESSON_ID'];
						$i++;
					}
				}


				http_response_code(200);
				echo json_encode(array("result" => array(0 => array("Ид" => $lesson_id, "ДомашнееЗадание" => $homework, "Тема" => $theme,
				"ВремяИд" => $time_id, "ПредметИд" => $subject_id, "Дата" => $lesson_date, "Статус" => $status,
									   "Пропуск" => $pass, "Замечание" => $note, "Оценки" => $arr))), JSON_UNESCAPED_UNICODE);

		    } else {
			    header("HTTP/1.0 400 Bad Request");
				echo json_encode(array('Exception' => array('Message' => "Отсутствуют данные по учебному занятию")), JSON_UNESCAPED_UNICODE);
		    }

		}

	}
}
