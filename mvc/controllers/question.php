<?php

use PhpOffice\PhpWord\Element\AbstractContainer;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;

class Question extends Controller
{
    public $cauHoiModel;
    public $cauTraLoiModel;
    public $nguoiDungModel;
    function __construct()
    {
        $this->cauHoiModel = $this->model("CauHoiModel");
        $this->cauTraLoiModel = $this->model("CauTraLoiModel");
        $this->nguoiDungModel = $this->model("NguoiDungModel");
        parent::__construct();
        require_once "./mvc/core/Pagination.php";
    }

    function default()
    {
        if (AuthCore::checkPermission("cauhoi", "view")) {
            $this->view("main_layout", [
                "Page" => "question",
                "Title" => "Câu hỏi",
                "Plugin" => [
                    "ckeditor" => 1,
                    "select" => 1,
                    "notify" => 1,
                    "sweetalert2" => 1,
                    "pagination" => [],
                    "jquery-validate" => 1,
                ],
                "Script" => "question",
                "user_id" => $_SESSION['user_id'],
            ]);
        } else {
            $this->view("single_layout", ["Page" => "error/page_404","Title" => "Lỗi !"]);
        }
    }
    function approve()
    {
        if (AuthCore::checkPermission("cauhoi", "update")) {
            $this->view("main_layout", [
                "Page" => "question_approve",
                "Title" => "Câu hỏi",
                "Plugin" => [
                    "ckeditor" => 1,
                    "select" => 1,
                    "notify" => 1,
                    "sweetalert2" => 1,
                    "pagination" => [],
                    "jquery-validate" => 1,
                ],
                "Script" => "question_approve",
                "user_id" => $_SESSION['user_id'],
            ]);
        } else {
            $this->view("single_layout", ["Page" => "error/page_404", "Title" => "Lỗi !"]);
        }
    }

    public function xulyDocx()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && AuthCore::checkPermission("cauhoi", "create")) {
            require_once 'vendor/autoload.php';
            $filename = $_FILES["fileToUpload"]["tmp_name"];
            $objReader = WordIOFactory::createReader('Word2007');
            $phpWord = $objReader->load($filename);
            $text = '';
            // Lấy kí tự từng đoạn
            function getWordText($element)
            {
                $result = '';
                if ($element instanceof AbstractContainer) {
                    foreach ($element->getElements() as $element) {
                        $result .= getWordText($element);
                    }
                } elseif ($element instanceof Text) {
                    $result .= $element->getText();
                }
                return $result;
            }

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    $text .= trim(getWordText($element));
                    $text .= "\\n";
                }
            }

            $text = rtrim($text, "\\n");
            substr($text, -1);
            $questions = explode("\\n\\n", $text);
            $arrques = array();
            for ($i = 0; $i < count($questions); $i++) {
                $data = explode("\\n", $questions[$i]);
                $arrques[$i]['level'] = substr($data[0], 1, 1);
                $arrques[$i]['question'] = substr(trim($data[0]), 4);
                $arrques[$i]['answer'] = ord(trim(substr($data[count($data) - 1], 8))) - 65 + 1;
                $arrques[$i]['option'] = array();
                for ($j = 1; $j < count($data) - 1; $j++) {
                    $arrques[$i]['option'][] = trim(substr($data[$j], 3));
                }
            }
            echo json_encode($arrques);
        }
    }
    public function xulyDocx2()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && AuthCore::checkPermission("cauhoi", "create")) {
            require_once 'vendor/autoload.php';
            $filename = $_FILES["fileToUpload"]["tmp_name"];
            $objReader = \PhpOffice\PhpWord\IOFactory::createReader('Word2007');
            $phpWord = $objReader->load($filename);

            function getWordText($element)
            {
                $result = '';
                if ($element instanceof \PhpOffice\PhpWord\Element\AbstractContainer) {
                    foreach ($element->getElements() as $child) {
                        $result .= getWordText($child);
                    }
                } elseif ($element instanceof \PhpOffice\PhpWord\Element\Text) {
                    $result .= $element->getText();
                }
                return $result;
            }

            $text = '';
            $answerMap = [];

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {

                    // Nếu là bảng chứa đáp án
                    if ($element instanceof \PhpOffice\PhpWord\Element\Table) {
                        foreach ($element->getRows() as $row) {
                            $cells = $row->getCells();
                            for ($i = 0; $i < count($cells) - 1; $i += 2) {
                                $qnum = trim(getWordText($cells[$i]));
                                $letter = trim(getWordText($cells[$i + 1]));
                                if (is_numeric($qnum) && preg_match('/^[A-Da-d]$/', $letter)) {
                                    $answerMap[(int)$qnum] = strtoupper($letter);
                                }
                            }
                        }
                    } else {
                        // Các phần còn lại (câu hỏi)
                        $text .= trim(getWordText($element)) . "\n";
                    }
                }
            }

            // Tách từng câu hỏi
            preg_match_all('/Câu\s*(\d+)\.\s*(.*?)((?=Câu\s*\d+\.|$))/s', $text, $questionMatches, PREG_SET_ORDER);

            $result = [];
            foreach ($questionMatches as $qMatch) {
                $qnum = (int)$qMatch[1];
                $content = trim($qMatch[2] . $qMatch[3]);

                // Tách dòng
                $lines = preg_split('/\r\n|\n|\r/', $content);
                $questionLines = [];
                $optionText = "";

                foreach ($lines as $line) {
                    if (preg_match('/^\s*[A-D]\./', $line)) {
                        $optionText .= " " . trim($line);
                    } else {
                        $questionLines[] = trim($line);
                    }
                }

                $questionText = implode(" ", $questionLines);

                // Tách các lựa chọn A. B. C. D.
                preg_match_all('/([A-D])\.\s*(.*?)(?=\s+[A-D]\.|$)/s', $optionText, $optionMatches);
                $options = array_map('trim', $optionMatches[2]);

                // Tìm đáp án đúng
                $correctLetter = isset($answerMap[$qnum]) ? $answerMap[$qnum] : null;
                $answerIndex = $correctLetter ? (ord($correctLetter) - ord('A') + 1) : null;

                $result[] = [
                    "level" => "1",
                    "question" => $questionText,
                    "option" => $options,
                    "answer" => $answerIndex
                ];
            }

            echo json_encode($result);
        }
    }

    public function addExcel()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && AuthCore::checkPermission("cauhoi", "create")) {
            require_once 'vendor/autoload.php';
            $inputFileName = $_FILES["fileToUpload"]["tmp_name"];
            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {
                die('Lỗi không thể đọc file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
            }
            $sheet = $objPHPExcel->setActiveSheetIndex(0);
            $Totalrow = $sheet->getHighestRow();
            $LastColumn = $sheet->getHighestColumn();
            $TotalCol = PHPExcel_Cell::columnIndexFromString($LastColumn);
            $data = [];
            for ($i = 2; $i <= $Totalrow; $i++) {
                //----Lặp cột
                for ($j = 0; $j < $TotalCol; $j++) {
                    // Tiến hành lấy giá trị của từng ô đổ vào mảng
                    $check = '';
                    if($j == 0){
                        $check = "level";
                        $data[$i - 2][$check] = $sheet->getCellByColumnAndRow($j, $i)->getValue();
                    } else if($j == 1){
                        $check = "question";
                        $data[$i - 2][$check] = $sheet->getCellByColumnAndRow($j, $i)->getValue();
                    } else if($j == $TotalCol-1){
                        $check = "answer";
                        $data[$i - 2][$check] = $sheet->getCellByColumnAndRow($j, $i)->getValue();
                    } else {
                        $check = "option";
                        $data[$i - 2][$check][] = $sheet->getCellByColumnAndRow($j, $i)->getValue();
                    }
                }
            }
            echo json_encode($data);
        }
    }

    public function addQues()
    {
        if (AuthCore::checkPermission("cauhoi", "create")) {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
              
                $machuong = $_POST['machuong'];
                $dokho = $_POST['dokho'];
                $noidung = $_POST['noidung'];
                $cautraloi = $_POST['cautraloi'];
                $nguoitao = $_SESSION['user_id'];
                $checkadmin = $this->nguoiDungModel->checkAdmin($nguoitao);
                $result = $this->cauHoiModel->create($noidung, $dokho,$machuong, $nguoitao, $checkadmin?1:0);
                $macauhoi = mysqli_insert_id($result);
                $check = '';
                foreach ($cautraloi as $x) {
                    $this->cauTraLoiModel->create($macauhoi, $x['content'], $x['check'] == 'true' ? 1 : 0);
                }
                echo $check;
            }
        }
    }

    public function addQuesFile()
    {
        if (AuthCore::checkPermission("cauhoi", "create")) {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $nguoitao = $_SESSION['user_id'];
                $checkadmin = $this->nguoiDungModel->checkAdmin($nguoitao);
                $chuong = $_POST['chuong'];
                $questions = $_POST["questions"];
                foreach ($questions as $question) {
                    $level = $question['level'];
                    $trangthai = $checkadmin?1:0;
                    $noidung = $question['question'];
                    $answer = $question['answer'];
                    $options = $question['option'];
                    $result = $this->cauHoiModel->create($noidung, $level, $chuong, $nguoitao,$trangthai);
                    $macauhoi = mysqli_insert_id($result);
                    $index = 1;
                    foreach ($options as $option) {
                        $check = 0;
                        if ($index == $answer) {
                            $check = 1;
                        }
                        $this->cauTraLoiModel->create($macauhoi, $option, $check);
                        $index++;
                    }
                }
            }
        }
    }


    public function getQuestion()
    {
        if (AuthCore::checkPermission("cauhoi", "view")) {
            if($_SERVER['REQUEST_METHOD'] == 'GET'){
                $result = $this->cauHoiModel->getAll();
                echo json_encode($result);    
            }
        }
    }
    public function getQuestionByClientReview()
    {
       
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $chuongs = $_POST["chuongs"];
                
                $socau = $_POST["socau"];
                $result = $this->cauHoiModel->getQuestionByClientReview($chuongs, $socau);
                echo json_encode($result);
            }
        
    }
    public function getAnserByClientReview()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $cauhois = $_POST["cauhois"]; // array of objects
            $macauhoiList = array_map(function ($q) {
                return $q['macauhoi'];
            }, $cauhois);
            $macauhoiStr = implode(",", $macauhoiList);

            $cautraloiList = $this->cauHoiModel->getAnserByClientReview($macauhoiStr);

            // Gắn vào từng câu hỏi
            foreach ($cauhois as &$q) {
                $q['cautraloi'] = $cautraloiList[$q['macauhoi']] ?? [];
            }

            echo json_encode($cauhois);
        }
    }

    public function count()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $chuongs = $_POST['chuongs'] ?? '';
            $total = $this->model("CauHoiModel")->countQuestion($chuongs);
            echo json_encode(["total" => $total]);
        }
    }

    public function delete()
    {
        if (AuthCore::checkPermission("cauhoi", "delete")) {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $id = $_POST['macauhoi'];
                $this->cauHoiModel->delete($id);
            }
        }
    }

    public function getQuestionById()
    {
        if (AuthCore::checkPermission("cauhoi", "view")) {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $id = $_POST['id'];
                $result = $this->cauHoiModel->getById($id);
                echo json_encode($result);
            }
        }
    }

    public function getAnswerById()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id = $_POST['id'];
            $result = $this->cauTraLoiModel->getAll($id);
            echo json_encode($result);
        }
    }

    public function editQuesion()
    {
        if (AuthCore::checkPermission("cauhoi", "update")) {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $id = $_POST['id'];
                $this->cauTraLoiModel->deletebyanswer($id);
                $mamon = $_POST['mamon'];
                $machuong = $_POST['machuong'];
                $dokho = $_POST['dokho'];
                $noidung = $_POST['noidung'];
                $cautraloi = $_POST['cautraloi'];
                $trangthai = $_POST['trangthai'];
                $nguoitao =0;
                $result = $this->cauHoiModel->update($id, $noidung, $dokho, $machuong, $nguoitao, $trangthai);
                $macauhoi = $id;
                foreach ($cautraloi as $x) {
                    $this->cauTraLoiModel->create($macauhoi, $x['content'], $x['check'] == 'true' ? 1 : 0);
                }
            }
        }
    }

    public function getTotalPage()
    {
        AuthCore::checkAuthentication();
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $content = $_POST['content'];
            $select = $_POST['selected'];
            echo $this->cauHoiModel->getTotalPage($content);
        }
    }

    public function getQuestionBySubject()
    {
        AuthCore::checkAuthentication();
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $mamonhoc = $_POST['mamonhoc'];
            $machuong = $_POST['machuong'];
            $dokho = $_POST['dokho'];
            $content = $_POST['content'];
            $page = $_POST['page'];
            $result = $this->cauHoiModel->getQuestionBySubject($mamonhoc,$machuong,$dokho,$content,$page);
            echo json_encode($result);
        }
    }
    
    public function getTotalPageQuestionBySubject()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $mamonhoc = $_POST['mamonhoc'];
            $machuong = $_POST['machuong'];
            $dokho = $_POST['dokho'];
            $content = $_POST['content'];
            $result = $this->cauHoiModel->getTotalPageQuestionBySubject($mamonhoc,$machuong,$dokho,$content);
            echo $result;
        }
    }

    public function getQuery($filter, $input, $args) {
        $result = $this->cauHoiModel->getQuery($filter, $input, $args);
        return $result;
    }

    public function getAnswersForMultipleQuestions() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $arr_question_id = $_POST['questions'];
            $result = $this->cauTraLoiModel->getAnswersForMultipleQuestions($arr_question_id);
            echo json_encode($result);
        }
    }

    public function getsoluongcauhoi()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $chuong = isset($_POST['chuong']) ? $_POST['chuong'] : array();
            $monhoc = $_POST['monhoc'];
            $dokho = $_POST['dokho'];
            $result = $this->cauHoiModel->getsoluongcauhoi($chuong,$monhoc,$dokho);
            echo $result;
        }
    }
}
