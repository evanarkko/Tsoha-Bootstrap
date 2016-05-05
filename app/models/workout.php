<?php

if (!class_exists('Workout')) {
class Workout extends BaseModel{
	public $id, $name, $trainer_id, $workout_time, $description;

	public function __construct($attributes){
		parent::__construct($attributes);
	}

	public static function all(){
		$query = DB::connection()->prepare('SELECT * FROM Workout');
		$query->execute();

		$rows = $query->fetchAll();
		$workouts = array();

		foreach($rows as $row){
			$workouts[] = new Workout(array(
				'id' => $row['id'],
				'name' => $row['name'],
				'trainer_id' => $row['trainerid'],
				'workout_time' => $row['workouttime'],
				'description' => $row['description']
			));
		}
		return $workouts;
	}

	public static function findCurrentUsersWorkouts(){
		$id = $_SESSION['user'];

		$query = DB::connection()->prepare('SELECT * FROM Workout WHERE trainerid = :id');
		$query->execute(array('id' => $id));

		$rows = $query->fetchAll();
		$workouts = array();

		foreach($rows as $row){
			$workouts[] = new Workout(array(
				'id' => $row['id'],
				'name' => $row['name'],
				'trainer_id' => $id,
				'workout_time' => $row['workouttime'],
				'description' => $row['description']
			));
		}
		return $workouts;
	}

	public static function find($id){
		$query = DB::connection()->prepare('SELECT * FROM Workout WHERE id = :id LIMIT 1');
		$query->execute(array('id' => $id));
		$row = $query->fetch();

		if($row){
			$workout = new Workout(array(
				'id' => $row['id'],
				'name' => $row['name'],
				'trainer_id' => $row['trainerid'],
				'workout_time' => $row['workouttime'],
				'description' => $row['description']
				));
			return $workout;
		}
		return null;
	}

	public function save(){
		if(!strcmp($this->name, ""))return;
		$query = DB::connection()->prepare('INSERT INTO Workout (Name, TrainerId, WorkoutTime, Description) VALUES (:name, :trainerid, NOW(), :description) RETURNING id');
		$query->execute(array('name' => $this->name, 'trainerid' => $this->trainer_id, 'description' => $this->description));
		// $row = $query->fetch();
		// $this->id = $row['id'];
		//NÄITÄ EHK TARTTEE MYÖH
	}

	public function destroyMUOKKAATÄMÄOIKEANLAISEKSI(){
		$query1 = DB::connection()->prepare('DELETE FROM ExerciseSet WHERE ExerciseId = :id');
		$query1->execute(array('id' => $this->id));

		$query = DB::connection()->prepare('DELETE FROM WeightExercise WHERE id = :id');
		$query->execute(array('id' => $this->id));
	}
}
}