<?php
/**
 * @link              https://pokamedia.com
 * @since             1.0.0
 * @package           Poka_Faqpage
 * @author Thien Tran <thientran2359@gmail.com>
 * 
 */
// Block direct access to file
defined('ABSPATH') or die('Not Authorized!');

class Poka_Faqpage {

    protected static $instance;

    public static function init() {
        is_null(self::$instance) AND self::$instance = new self;
        return self::$instance;
    }

    public function __construct() {
        // Plugin Actions
        add_action('plugins_loaded', array(&$this, 'plugins_loaded'));
        add_action('after_setup_theme', array($this, 'after_setup_theme'), 999);
        add_action('wp_head', array($this, 'wp_head'));

        add_action('admin_init', array($this, 'add_meta_boxes'), 1);
        add_action('save_post', array($this, 'repeatable_meta_box_save'));

        add_shortcode('poka-faqpage-content', array($this, 'poka_faqpage_content'));
    }

    public function plugins_loaded($param) {
        load_plugin_textdomain("poka-faqpage", false, dirname(POKA_FAQPAGE_DIRECTORY_BASENAME) . '/languages/');
    }

    public function after_setup_theme() {
        add_action('the_content', array($this, 'the_content'));
    }

    public function get_sample_options() {
        $options = array(
            'Option 1' => 'option1',
            'Option 2' => 'option2',
            'Option 3' => 'option3',
            'Option 4' => 'option4',
        );

        return $options;
    }

    public function add_meta_boxes() {
        add_meta_box('poka-faqs-fields', __('FAQs structured data', 'poka-faqpage'), array($this, 'repeatable_meta_box_display'), 'post', 'normal', 'high');
    }

    public function repeatable_meta_box_display() {
        global $post;
        $faqpage_questions = get_post_meta($post->ID, 'faqpage_questions', true);
        wp_nonce_field('repeatable_meta_box_nonce', 'repeatable_meta_box_nonce');
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('#faqs-add-row').on('click', function () {
                    var row = $('.empty-row.screen-reader-text').clone(true);
                    row.removeClass('empty-row screen-reader-text');
                    row.insertBefore('#repeatable-fieldset-one tbody>tr:last');
                    return false;
                });

                $('.faqs-remove-row').on('click', function () {
                    if (confirm('Confirm Delete Question!')) {
                        $(this).parents('tr').remove();
                        return false;
                    } else {
                        return false;
                    }
                });
            });
        </script>
        <style>
            #repeatable-fieldset-one tr {
                padding-bottom: 10px;
            }
        </style>
        <div class="row" style="margin-bottom: 15px;padding: 15px 0;display: flex;border-bottom: solid 1px #ccc;">
            <span class="" style="width: 20%;line-height: 30px;font-weight: bold;"><?php echo __('Title Tag', 'poka-faqpage'); ?>:</span>
            <select name="faqpage_tag_title" class="faqpage-tag-title">
                <option value="h2" <?php if (!empty($faqpage_questions['tag']) && $faqpage_questions['tag'] == 'h2') echo 'selected'; ?>>H2</option>
                <option value="h3" <?php if (!empty($faqpage_questions['tag']) && $faqpage_questions['tag'] == 'h3') echo 'selected'; ?>>H3</option>
                <option value="h4" <?php if (!empty($faqpage_questions['tag']) && $faqpage_questions['tag'] == 'h4') echo 'selected'; ?>>H4</option>
            </select>
        </div>
        <div class="row" style="margin-bottom: 15px;padding: 15px 0;display: flex;border-bottom: solid 1px #ccc;">
            <span class="" style="width: 20%;line-height: 30px;font-weight: bold;"><?php echo __('Title text', 'poka-faqpage'); ?>:</span>
            <input type="text" class="widefat" name="faqpage_tag_title_text" value="<?php if ($faqpage_questions['tag_text'] !== '') echo esc_attr($faqpage_questions['tag_text']); ?>"  placeholder="<?php echo __('Frequently Asked Question', 'poka-faqpage'); ?>" />
        </div>

        <table id="repeatable-fieldset-one" width="100%">
            <thead>
                <tr>
                    <th width="40%"><?php echo __('Question', 'poka-faqpage'); ?></th>
                    <th width="60%"><?php echo __('Answer', 'poka-faqpage'); ?></th>
                </tr>
            </thead>
            <tbody style="vertical-align: top;">
                <?php
                if (is_array($faqpage_questions) && $faqpage_questions['questions']) :

                    foreach ($faqpage_questions['questions'] as $questions) {
                        ?>
                        <tr>
                            <td>
                                <input type="text" class="widefat" name="faqpage_question[]" value="<?php if ($questions['faqpage_question'] != '') echo esc_attr($questions['faqpage_question']); ?>" />
                                <a class="button faqs-remove-row" href="#"><?php echo __('Remove', 'poka-faqpage'); ?> <i class="fa fa-times" aria-hidden="true"></i></a>
                            </td>
                            <td>
                                <textarea class="widefat" name="faqpage_answer[]" rows="3" cols="25" style="widows: 100%"><?php
                                    if ($questions['faqpage_answer'] != '')
                                        echo esc_attr($questions['faqpage_answer']);
                                    else
                                        echo '';
                                    ?></textarea>
                            </td>
                        </tr>
                        <?php
                    }
                else :
                    // show a blank one
                    ?>
                    <tr>
                        <td>
                            <input type="text" class="widefat" name="faqpage_question[]" />
                            <a class="button faqs-remove-row" href="#"><?php echo __('Remove', 'poka-faqpage'); ?> <i class="fa fa-times" aria-hidden="true"></i></a>
                        </td>
                        <td><textarea class="widefat" name="faqpage_answer[]" value="" rows="3" cols="25" style="widows: 100%"></textarea></td>
                    </tr>
                <?php endif; ?>
                <tr class="empty-row screen-reader-text">
                    <td>
                        <input type="text" class="widefat" name="faqpage_question[]" />
                        <a class="button faqs-remove-row" href="#"><?php echo __('Remove', 'poka-faqpage'); ?> <i class="fa fa-times" aria-hidden="true"></i></a>
                    </td>
                    <td><textarea class="widefat" name="faqpage_answer[]" value="" rows="3" cols="25" style="widows: 100%"></textarea></td>
                </tr>
            </tbody>
        </table>

        <p>
            <a id="faqs-add-row" class="button" href="#"><?php echo __('Add question', 'poka-faqpage'); ?></a>
            <a class="button button-primary" target="_blank" href="https://search.google.com/test/rich-results?utm_campaign=devsite&utm_medium=jsonld&utm_source=faq-page&url=<?php echo urlencode(get_permalink($post->ID)); ?>"><?php echo __('Check Google Richsnippet', 'poka-faqpage'); ?></a>
        </p>
        <?php
    }

    public function repeatable_meta_box_save($post_id) {
        if (!isset($_POST['repeatable_meta_box_nonce']) ||
                !wp_verify_nonce($_POST['repeatable_meta_box_nonce'], 'repeatable_meta_box_nonce'))
            return;

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;

        if (!current_user_can('edit_post', $post_id))
            return;

        $old = get_post_meta($post_id, 'faqpage_questions', true);
        $new = array();

        $faqpage_questions = $_POST['faqpage_question'];
        $faqpage_answers = $_POST['faqpage_answer'];
        $faqpage_tag_title = $_POST['faqpage_tag_title'];
        $faqpage_tag_title_text = $_POST['faqpage_tag_title_text'];

        $new['tag'] = $faqpage_tag_title;
        $new['tag_text'] = $faqpage_tag_title_text;

        $count = count($faqpage_questions);

        for ($i = 0; $i < $count; $i++) {
            if ($faqpage_questions[$i] != '') :
                $new['questions'][$i]['faqpage_question'] = stripslashes(strip_tags($faqpage_questions[$i]));

                if ($faqpage_answers[$i] == '')
                    $new['questions'][$i]['faqpage_answer'] = '';
                else
                    $new['questions'][$i]['faqpage_answer'] = stripslashes($faqpage_answers[$i]); // and however you want to sanitize
            endif;
        }

        if (!empty($new) && $new != $old)
            update_post_meta($post_id, 'faqpage_questions', $new);
        elseif (empty($new) && $old)
            delete_post_meta($post_id, 'faqpage_questions', $old);
    }

    public function wp_head() {
        global $post;
        $faqpage_questions = get_post_meta($post->ID, 'faqpage_questions', true);
        if (empty($faqpage_questions)) {
            return;
        }

        $mainEntity = array();
        foreach ($faqpage_questions['questions'] as $faqpage_question) {
            $mainEntity[] = array(
                "@type" => "Question",
                "name" => $faqpage_question['faqpage_question'],
                "acceptedAnswer" => array(
                    "@type" => "Answer",
                    "text" => $faqpage_question['faqpage_answer']
                )
            );
        }

        $application = array(
            "@context" => "https://schema.org",
            "@type" => "FAQPage",
            "mainEntity" => $mainEntity
        );
        echo '<script type="application/ld+json">' . json_encode($application) . '</script>';
    }

    public function the_content($content) {
        global $post;
        $faqpage_questions = get_post_meta($post->ID, 'faqpage_questions', true);
        if (empty($faqpage_questions) || empty($faqpage_questions['questions'])) {
            return $content;
        }
        $tag_title = !empty($faqpage_questions['tag']) ? $faqpage_questions['tag'] : 'h2';
        $tag_title_text = !empty($faqpage_questions['tag_text']) ? $faqpage_questions['tag_text'] : __('Frequently Asked Question', 'poka-faqpage');
        switch ($tag_title) {
            case $tag_title = 'h3':
                $tag_questions = 'h4';
                break;
            case $tag_title = 'h4':
                $tag_questions = 'h5';
                break;
            default:
                $tag_questions = 'h3';
                break;
        }
        ob_start();
        ?>
        <style>
            .faqpge-title {
                font-weight: bold;
                font-size: 18px;
                margin-bottom: 15px;
                color: #4f2f1b;
                text-align: justify;
            }
            .faqpge-content {
                padding: 1px 0;
                text-align: justify;
            }
            .faqpge-content .faqpge-question-content {
                margin: 15px 0;
                display: block;
            }
            .faqpge-content .faqpge-question-text {
                display: inline;
                font-size: 18px;
                font-weight: bold;
                text-decoration: underline;
            }
            .faqpge-content .faqpge-question {
                display: inline;
                font-size: 18px;
                font-weight: bold;
            }
            .faqpge-content .faqpge-answer {
                font-size: 18px;
            }
            .faqpge-content .faqpge-answer-text {
                font-size: 18px;
                font-weight: bold !important;
                text-decoration: underline;
            }
            .faqpge-content >*:last-child {
                margin-bottom: 0;
            }
            @media screen and (max-width: 782px) {
                .faqpge-title, .faqpge-content .faqpge-question-text, .faqpge-content .faqpge-question, .faqpge-content .faqpge-answer, .faqpge-content .faqpge-answer-text {
                    font-size: 20px;
                }
            }
        </style>
        <?php
        echo '<' . $tag_title . ' class="poka-notoc faqpge-title">' . $tag_title_text . '</' . $tag_title . '>';
        echo '<div class="faqpge-content">';
        foreach ($faqpage_questions['questions'] as $question) {
            if (empty($question['faqpage_question']) && empty($question['faqpage_answer']))
                continue;
            echo '<div class="faqpge-question-content"><div class="faqpge-question-text">' . __("Question:", 'poka-faqpage') . '</div><' . $tag_questions . ' class="poka-notoc faqpge-question"> ' . $question['faqpage_question'] . '</' . $tag_questions . '></div>';
            echo '<p class="faqpge-answer"><span class="faqpge-answer-text">' . __("Reply:", 'poka-faqpage') . '</span> ' . $question['faqpage_answer'] . '</p>';
        }
        echo '</div>';
        $faqpage_questions_content = ob_get_clean();

        return $content . $faqpage_questions_content;
    }

    public function poka_faqpage_content() {
        if (!is_singular('post'))
            return;
        $content = get_the_content();
        return $this->the_content($content);
    }

}

new Poka_Faqpage();
