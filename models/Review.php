<?php

    class Review {

        public $id;
        public $rating;
        public $review;
        public $users_id;
        public $movies_id;
        public $user;
    }

    interface ReviewDAOInterface  {

        public function buildReview($data);
        public function create($review);
        public function getMoviesReview($id);
        public function hasAlreadyReviewed($id, $users_id);
        public function getRatings($id);
    }