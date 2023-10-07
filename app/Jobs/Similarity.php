<?php

namespace App\Jobs;

use App\Models\Exam;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;


class Similarity implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $exam;

    private $question_id;

    private $student_answer;

    private $student_id;

    /**
     * Create a new job instance.
     */
    public function __construct(Exam $exam, int $student_id, int $question_id, string $student_answer)
    {
        $this->exam = $exam->withoutRelations();
        $this->student_id = $student_id;
        $this->question_id = $question_id;
        $this->student_answer = $student_answer;
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $correct_answer = $this->exam->questions()->where('id', $this->question_id)->first()->answer->answer;
        $fastapi_url = config('app.fastapi_url');
        $response = Http::retry(3,100)->get("$fastapi_url/similarity", [
            'text1' => $correct_answer,
            'text2' => $this->student_answer,
        ]);
        if($response->failed()){
            $similarity = $this->similarity($correct_answer, $this->student_answer);
        }else{
            $similarity = $response['similarity'];
        }

        $this->exam->studentAnswers()->where('question_id', $this->question_id)->where('student_id', $this->student_id)->update(['similarity' => $similarity]);
    }

    public function similarity($str1, $str2) {
        $len1 = strlen($str1);
        $len2 = strlen($str2);
        
        $max = max($len1, $len2);
        $similarity = $i = $j = 0;
        
        while (($i < $len1) && isset($str2[$j])) {
            if ($str1[$i] == $str2[$j]) {
                $similarity++;
                $i++;
                $j++;
            } elseif ($len1 < $len2) {
                $len1++;
                $j++;
            } elseif ($len1 > $len2) {
                $i++;
                $len1--;
            } else {
                $i++;
                $j++;
            }
        }

        return round($similarity / $max, 2);
    }
}
