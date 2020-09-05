<?php
/**
 * Copyright notice
 *
 * (c) 2009-2017 Net Inventors - Agentur für digitale Medien GmbH
 * All rights reserved
 *
 * This script is part of the Spirit-Project.
 * The Spirit-Project is property of the Net Inventors GmbH and
 * may not be used in projects not related to the Net Inventors
 * without explicit permission by the authors.
 *
 * PHP version 5
 *
 * @package    NetiFoundation
 * @subpackage NetiFoundation/MailTemplateData.php
 * @author     bmueller
 * @copyright  2017 Net Inventors GmbH
 * @license    proprietary http://www.netinventors.de
 * @version    GIT: $revision
 * @link       http://www.netinventors.de
 */

namespace NetiFoundation\Struct;

class MailTemplateData extends AbstractClass
{
    /**
     * @var int
     */
    protected $mailTemplateId;

    /**
     * @var string
     */
    protected $templateName;

    /**
     * @var string
     */
    protected $fromMail;

    /**
     * @var string
     */
    protected $fromName;

    /**
     * @var string
     */
    protected $subject;

    /**
     * @var bool
     */
    protected $isHtml;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var string
     */
    protected $contentHtml;

    /**
     * @var array
     */
    protected $bcc = [];

    /**
     * @var array
     */
    protected $contextData = [];

    /**
     * @var \Zend_Mime_Part[]
     */
    protected $attachments = [];

    /**
     * @return array
     */
    public function getBcc()
    {
        return $this->bcc;
    }

    /**
     * @param array $bcc
     *
     * @return MailTemplateData
     */
    public function setBcc(array $bcc)
    {
        if (!\filter_var_array($bcc, \FILTER_VALIDATE_EMAIL)) {
            $bcc = [];
        }

        $this->bcc = $bcc;

        return $this;
    }

    /**
     * @param string $bcc
     *
     * @return MailTemplateData
     */
    public function addBcc($bcc)
    {
        if (\filter_var($bcc, \FILTER_VALIDATE_EMAIL)) {
            $this->bcc[] = $bcc;
        }

        return $this;
    }

    /**
     * Gets the value of templateName from the record
     *
     * @return string
     */
    public function getTemplateName()
    {
        return $this->templateName;
    }

    /**
     * Gets the value of fromMail from the record
     *
     * @return string
     */
    public function getFromMail()
    {
        return $this->fromMail;
    }

    /**
     * Gets the value of fromName from the record
     *
     * @return string
     */
    public function getFromName()
    {
        return $this->fromName;
    }

    /**
     * Gets the value of subject from the record
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Gets the value of isHtml from the record
     *
     * @return boolean
     */
    public function isIsHtml()
    {
        return $this->isHtml;
    }

    /**
     * Gets the value of content from the record
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Gets the value of contentHtml from the record
     *
     * @return string
     */
    public function getContentHtml()
    {
        return $this->contentHtml;
    }

    /**
     * Gets the value of contextData from the record
     *
     * @return array
     */
    public function getContextData()
    {
        return $this->contextData;
    }

    /**
     * Gets the value of mailTemplateId from the record
     *
     * @return int
     */
    public function getMailTemplateId()
    {
        return $this->mailTemplateId;
    }

    /**
     * Gets the value of attachments from the record
     *
     * @return \Zend_Mime_Part[]
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * Add a single attachment to the existing attachments
     * If the attachment is passed as array, the key 'content' is mandatory.
     * The keys 'type', 'encoding' and 'filename' are optional.
     *
     * @param \Zend_Mime_Part|array $attachment
     *
     * @return $this
     */
    public function addAttachment($attachment)
    {
        if (is_array($attachment)) {
            $mimePart              = new \Zend_Mime_Part($attachment['content']);
            $mimePart->type        = isset($attachment['type']) ? $attachment['type'] : 'text/plain';
            $mimePart->disposition = \Zend_Mime::DISPOSITION_ATTACHMENT;
            $mimePart->encoding    = isset($attachment['encoding']) ? $attachment['encoding']
                : \Zend_Mime::ENCODING_BASE64;
            $mimePart->filename    = isset($attachment['filename']) ? $attachment['filename'] : 'attachment';
            $attachment            = $mimePart;
        }

        if ($attachment instanceof \Zend_Mime_Part) {
            $this->attachments[] = $attachment;
        }

        return $this;
    }
}
