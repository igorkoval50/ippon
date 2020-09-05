<?php
    namespace digi1Individualpromotionbanner\Models;

    use Shopware\Components\Model\ModelEntity;
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\ORM\Mapping as ORM;
    use Symfony\Component\Validator\Constraints as Assert;

    /**
     * @ORM\Entity
     * @ORM\Table(name="s_plugin_digi1_individualpromotionbanner")
     */
    class Promotionbanner extends ModelEntity {
        /**
         * @var integer $id
         *
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private $id;

        /**
         * @var boolean $active
         *
         * @ORM\Column(type="boolean", nullable=true)
         */
        private $active;

        /**
         * @var integer $positionnumber
         *
         * @ORM\Column(type="integer")
         */
        private $positionnumber;

        /**
         * @var string $label
         *
         * @ORM\Column(type="string", length=100, nullable=true)
         */
        private $label;

        /**
         * @var string $backgroundimage
         *
         * @ORM\Column(type="text", nullable=true)
         */
        private $backgroundimage;

        /**
         * @var integer $backgroundposition
         *
         * @ORM\Column(type="integer")
         */
        private $backgroundposition;

        /**
         * @var integer $backgroundsize
         *
         * @ORM\Column(type="integer")
         */
        private $backgroundsize;

        /**
         * @var string $backgroundcolor
         *
         * @ORM\Column(type="string", length=50, nullable=true)
         */
        private $backgroundcolor;

        /**
         * @var integer $backgroundopacity
         *
         * @ORM\Column(type="integer")
         */
        private $backgroundopacity;

        /**
         * @var boolean $collapsible
         *
         * @ORM\Column(type="boolean", nullable=true)
         */
        private $collapsible;

        /**
         * @var integer $collapsiblecookielifetime
         *
         * @ORM\Column(type="integer")
         */
        private $collapsiblecookielifetime;

        /**
         * @var boolean $hidecollapseicon
         *
         * @ORM\Column(type="boolean", nullable=true)
         */
        private $hidecollapseicon;

        /**
         * @var string $collapseiconbackgroundcolor
         *
         * @ORM\Column(type="string", length=50, nullable=true)
         */
        private $collapseiconbackgroundcolor;

        /**
         * @var string $collapseiconfontcolor
         *
         * @ORM\Column(type="string", length=10, nullable=true)
         */
        private $collapseiconfontcolor;

        /**
         * @var integer $position
         *
         * @ORM\Column(type="integer")
         */
        private $position;

        /**
         * @var integer $shop_id
         *
         * @ORM\Column(name="shop_id", type="integer")
         */
        private $shop_id;

        /**
         * @var
         * @ORM\ManyToOne(targetEntity="Shopware\Models\Shop\Shop")
         * @ORM\JoinColumn(name="shop_id", referencedColumnName="id")
         */
        private $shop;

        /**
         * @var boolean $showinallshops
         *
         * @ORM\Column(type="boolean", nullable=true)
         */
        private $showinallshops;

        /**
         * @var integer $showoncontroller
         *
         * @ORM\Column(type="integer")
         */
        private $showoncontroller;

        /**
         * @var string $cssclass
         *
         * @ORM\Column(type="string", length=100, nullable=true)
         */
        private $cssclass;

        /**
         * @var integer $modalboxtimedelay
         *
         * @ORM\Column(type="integer")
         */
        private $modalboxtimedelay;

        /**
         * @var string $percentagebackgroundcolor
         *
         * @ORM\Column(type="string", length=50, nullable=true)
         */
        private $percentagebackgroundcolor;

        /**
         * @var string $percentagefontcolor
         *
         * @ORM\Column(type="string", length=10, nullable=true)
         */
        private $percentagefontcolor;

        /**
         * @var string $percentagecssclass
         *
         * @ORM\Column(type="string", length=100, nullable=true)
         */
        private $percentagecssclass;

        /**
         * @var integer $percentagealignment
         *
         * @ORM\Column(type="integer")
         */
        private $percentagealignment;

        /**
         * @var integer $percentagewidth
         *
         * @ORM\Column(type="integer")
         */
        private $percentagewidth;

        /**
         * @var string $percentagepadding
         *
         * @ORM\Column(type="string", length=50, nullable=true)
         */
        private $percentagepadding;

        /**
         * @var string $percentage
         *
         * @ORM\Column(type="text", nullable=true)
         */
        private $percentage;

        /**
         * @var string $contentbackgroundcolor
         *
         * @ORM\Column(type="string", length=50, nullable=true)
         */
        private $contentbackgroundcolor;

        /**
         * @var string $contentpadding
         *
         * @ORM\Column(type="string", length=50, nullable=true)
         */
        private $contentpadding;

        /**
         * @var string $contentcssclass
         *
         * @ORM\Column(type="string", length=100, nullable=true)
         */
        private $contentcssclass;

        /**
         * @var string $headlinefontcolor
         *
         * @ORM\Column(type="string", length=10, nullable=true)
         */
        private $headlinefontcolor;

        /**
         * @var integer $headlinealignment
         *
         * @ORM\Column(type="integer")
         */
        private $headlinealignment;

        /**
         * @var integer $headlinewidth
         *
         * @ORM\Column(type="integer")
         */
        private $headlinewidth;

        /**
         * @var string $headline
         *
         * @ORM\Column(type="text", nullable=true)
         */
        private $headline;

        /**
         * @var string $txtfontcolor
         *
         * @ORM\Column(type="string", length=10, nullable=true)
         */
        private $txtfontcolor;

        /**
         * @var integer $txtalignment
         *
         * @ORM\Column(type="integer")
         */
        private $txtalignment;

        /**
         * @var integer $txtwidth
         *
         * @ORM\Column(type="integer")
         */
        private $txtwidth;

        /**
         * @var string $txt
         *
         * @ORM\Column(type="text", nullable=true)
         */
        private $txt;

        /**
         * @var string $completelinking
         *
         * @ORM\Column(type="boolean", nullable=true)
         */
        private $completelinking;

        /**
         * @var string $linkbelowcontent
         *
         * @ORM\Column(type="boolean", nullable=true)
         */
        private $linkbelowcontent;

        /**
         * @var string $linkbackgroundcolor
         *
         * @ORM\Column(type="string", length=50, nullable=true)
         */
        private $linkbackgroundcolor;

        /**
         * @var string $linkpadding
         *
         * @ORM\Column(type="string", length=50, nullable=true)
         */
        private $linkpadding;

        /**
         * @var boolean $target
         *
         * @ORM\Column(type="boolean", nullable=true)
         */
        private $target;

        /**
         * @var string $link
         *
         * @ORM\Column(type="string", length=100, nullable=true)
         */
        private $link;

        /**
         * @var string $linkcssclass
         *
         * @ORM\Column(type="string", length=100, nullable=true)
         */
        private $linkcssclass;

        /**
         * @var boolean $linktransparent
         *
         * @ORM\Column(type="boolean", nullable=true)
         */
        private $linktransparent;

        /**
         * @var string $linkbgcolor
         *
         * @ORM\Column(type="string", length=50, nullable=true)
         */
        private $linkbgcolor;

        /**
         * @var string $linkfontcolor
         *
         * @ORM\Column(type="string", length=10, nullable=true)
         */
        private $linkfontcolor;

        /**
         * @var string $linkbordercolor
         *
         * @ORM\Column(type="string", length=10, nullable=true)
         */
        private $linkbordercolor;

        /**
         * @var string $linktext
         *
         * @ORM\Column(type="string", length=100, nullable=true)
         */
        private $linktext;

        /**
         * @var integer $linkalignment
         *
         * @ORM\Column(type="integer")
         */
        private $linkalignment;

        /**
         * @var integer $linkwidth
         *
         * @ORM\Column(type="integer")
         */
        private $linkwidth;

        /**
         * @var \DateTime $displaydatefrom
         *
         * @ORM\Column(type="datetime", nullable=true)
         */
        private $displaydatefrom = null;

        /**
         * @var \DateTime $displaydateto
         *
         * @ORM\Column(type="datetime", nullable=true)
         */
        private $displaydateto = null;

        /**
         * @var boolean $hideinsmartphoneportrait
         *
         * @ORM\Column(type="boolean", nullable=true)
         */
        private $hideinsmartphoneportrait;

        /**
         * @var boolean $hideinsmartphonelandscape
         *
         * @ORM\Column(type="boolean", nullable=true)
         */
        private $hideinsmartphonelandscape;

        /**
         * @var boolean $hideintabletportrait
         *
         * @ORM\Column(type="boolean", nullable=true)
         */
        private $hideintabletportrait;

        /**
         * @var boolean $hideintabletlandscape
         *
         * @ORM\Column(type="boolean", nullable=true)
         */
        private $hideintabletlandscape;

        /**
         * @var boolean $hideindesktop
         *
         * @ORM\Column(type="boolean", nullable=true)
         */
        private $hideindesktop;

        /**
         * @return int
         */
        public function getId() {
            return $this->id;
        }

        /**
         * @param boolean $active
         */
        public function setActive($active) {
            $this->active = $active;
        }

        /**
         * @return boolean
         */
        public function getActive() {
            return $this->active;
        }

        /**
         * @param string $positionnumber
         */
        public function setPositionnumber($positionnumber) {
            $this->positionnumber = $positionnumber;
        }

        /**
         * @return string
         */
        public function getPositionnumber() {
            return $this->positionnumber;
        }

        /**
         * @param string $label
         */
        public function setLabel($label) {
            $this->label = $label;
        }

        /**
         * @return string
         */
        public function getLabel() {
            return $this->label;
        }

        /**
         * @param string $backgroundimage
         */
        public function setBackgroundimage($backgroundimage) {
            $this->backgroundimage = $backgroundimage;
        }

        /**
         * @return string
         */
        public function getBackgroundimage() {
            return $this->backgroundimage;
        }

        /**
         * @param mixed $backgroundposition
         */
        public function setBackgroundposition($backgroundposition) {
            $this->backgroundposition = $backgroundposition;
        }

        /**
         * @return mixed
         */
        public function getBackgroundposition() {
            return $this->backgroundposition;
        }

        /**
         * @param mixed $backgroundsize
         */
        public function setBackgroundsize($backgroundsize) {
            $this->backgroundsize = $backgroundsize;
        }

        /**
         * @return mixed
         */
        public function getBackgroundsize() {
            return $this->backgroundsize;
        }

        /**
         * @param string $backgroundcolor
         */
        public function setBackgroundcolor($backgroundcolor) {
            $this->backgroundcolor = $backgroundcolor;
        }

        /**
         * @return string
         */
        public function getBackgroundcolor() {
            return $this->backgroundcolor;
        }

        /**
         * @param mixed $backgroundopacity
         */
        public function setBackgroundopacity($backgroundopacity) {
            $this->backgroundopacity = $backgroundopacity;
        }

        /**
         * @return mixed
         */
        public function getBackgroundopacity() {
            return $this->backgroundopacity;
        }

        /**
         * @param boolean $collapsible
         */
        public function setCollapsible($collapsible) {
            $this->collapsible = $collapsible;
        }

        /**
         * @return boolean
         */
        public function getCollapsible() {
            return $this->collapsible;
        }

        /**
         * @param integer $collapsiblecookielifetime
         */
        public function setCollapsiblecookielifetime($collapsiblecookielifetime) {
            $this->collapsiblecookielifetime = $collapsiblecookielifetime;
        }

        /**
         * @return integer
         */
        public function getCollapsiblecookielifetime() {
            return $this->collapsiblecookielifetime;
        }

        /**
         * @param boolean $hidecollapseicon
         */
        public function setHidecollapseicon($hidecollapseicon) {
            $this->hidecollapseicon = $hidecollapseicon;
        }

        /**
         * @return boolean
         */
        public function getHidecollapseicon() {
            return $this->hidecollapseicon;
        }

        /**
         * @param string $collapseiconbackgroundcolor
         */
        public function setCollapseiconbackgroundcolor($collapseiconbackgroundcolor) {
            $this->collapseiconbackgroundcolor = $collapseiconbackgroundcolor;
        }

        /**
         * @return string
         */
        public function getCollapseiconbackgroundcolor() {
            return $this->collapseiconbackgroundcolor;
        }

        /**
         * @param string $collapseiconfontcolor
         */
        public function setCollapseiconfontcolor($collapseiconfontcolor) {
            $this->collapseiconfontcolor = $collapseiconfontcolor;
        }

        /**
         * @return string
         */
        public function getCollapseiconfontcolor() {
            return $this->collapseiconfontcolor;
        }

        /**
         * @param integer $position
         */
        public function setPosition($position) {
            $this->position = $position;
        }

        /**
         * @return integer
         */
        public function getPosition() {
            return $this->position;
        }

        /**
         * @param string $shop_id
         */
        public function setShopId($shop_id) {
            $this->shop_id = $shop_id;
        }

        /**
         * @return int
         */
        public function getShopId() {
            return $this->shop_id;
        }

        /**
         * @param mixed $shop
         */
        public function setShop($shop) {
            $this->shop = $shop;
        }

        /**
         * @return mixed
         */
        public function getShop() {
            return $this->shop;
        }

        /**
         * @param boolean $showinallshops
         */
        public function setShowinallshops($showinallshops) {
            $this->showinallshops = $showinallshops;
        }

        /**
         * @return boolean
         */
        public function getShowinallshops() {
            return $this->showinallshops;
        }

        /**
         * @param string $showoncontroller
         */
        public function setShowoncontroller($showoncontroller) {
            $this->showoncontroller = $showoncontroller;
        }

        /**
         * @return string
         */
        public function getShowoncontroller() {
            return $this->showoncontroller;
        }

        /**
         * @param string $cssclass
         */
        public function setCssclass($cssclass) {
            $this->cssclass = $cssclass;
        }

        /**
         * @return string
         */
        public function getCssclass() {
            return $this->cssclass;
        }

        /**
         * @param mixed $modalboxtimedelay
         */
        public function setModalboxtimedelay($modalboxtimedelay) {
            $this->modalboxtimedelay = $modalboxtimedelay;
        }

        /**
         * @return mixed
         */
        public function getModalboxtimedelay() {
            return $this->modalboxtimedelay;
        }

        /**
         * @param string $percentagebackgroundcolor
         */
        public function setPercentagebackgroundcolor($percentagebackgroundcolor) {
            $this->percentagebackgroundcolor = $percentagebackgroundcolor;
        }

        /**
         * @return string
         */
        public function getPercentagebackgroundcolor() {
            return $this->percentagebackgroundcolor;
        }

        /**
         * @param string $percentagefontcolor
         */
        public function setPercentagefontcolor($percentagefontcolor) {
            $this->percentagefontcolor = $percentagefontcolor;
        }

        /**
         * @return string
         */
        public function getPercentagefontcolor() {
            return $this->percentagefontcolor;
        }

        /**
         * @param string $percentagecssclass
         */
        public function setPercentagecssclass($percentagecssclass) {
            $this->percentagecssclass = $percentagecssclass;
        }

        /**
         * @return string
         */
        public function getPercentagecssclass() {
            return $this->percentagecssclass;
        }

        /**
         * @param string $percentagealignment
         */
        public function setPercentagealignment($percentagealignment) {
            $this->percentagealignment = $percentagealignment;
        }

        /**
         * @return string
         */
        public function getPercentagealignment() {
            return $this->percentagealignment;
        }

        /**
         * @param string $percentagewidth
         */
        public function setPercentagewidth($percentagewidth) {
            $this->percentagewidth = $percentagewidth;
        }

        /**
         * @return string
         */
        public function getPercentagewidth() {
            return $this->percentagewidth;
        }

        /**
         * @param string $percentagepadding
         */
        public function setPercentagepadding($percentagepadding) {
            $this->percentagepadding = $percentagepadding;
        }

        /**
         * @return string
         */
        public function getPercentagepadding() {
            return $this->percentagepadding;
        }

        /**
         * @param string $percentage
         */
        public function setPercentage($percentage) {
            $this->percentage = $percentage;
        }

        /**
         * @return string
         */
        public function getPercentage() {
            return $this->percentage;
        }

        /**
         * @param string $contentbackgroundcolor
         */
        public function setContentbackgroundcolor($contentbackgroundcolor) {
            $this->contentbackgroundcolor = $contentbackgroundcolor;
        }

        /**
         * @return string
         */
        public function getContentbackgroundcolor() {
            return $this->contentbackgroundcolor;
        }

        /**
         * @param string $contentpadding
         */
        public function setContentpadding($contentpadding) {
            $this->contentpadding = $contentpadding;
        }

        /**
         * @return string
         */
        public function getContentpadding() {
            return $this->contentpadding;
        }

        /**
         * @param string $contentcssclass
         */
        public function setContentcssclass($contentcssclass) {
            $this->contentcssclass = $contentcssclass;
        }

        /**
         * @return string
         */
        public function getContentcssclass() {
            return $this->contentcssclass;
        }

        /**
         * @param string $headlinefontcolor
         */
        public function setHeadlinefontcolor($headlinefontcolor) {
            $this->headlinefontcolor = $headlinefontcolor;
        }

        /**
         * @return string
         */
        public function getHeadlinefontcolor() {
            return $this->headlinefontcolor;
        }

        /**
         * @param string $headlinealignment
         */
        public function setHeadlinealignment($headlinealignment) {
            $this->headlinealignment = $headlinealignment;
        }

        /**
         * @return string
         */
        public function getHeadlinealignment() {
            return $this->headlinealignment;
        }

        /**
         * @param string $headlinewidth
         */
        public function setHeadlinewidth($headlinewidth) {
            $this->headlinewidth = $headlinewidth;
        }

        /**
         * @return string
         */
        public function getHeadlinewidth() {
            return $this->headlinewidth;
        }

        /**
         * @param string $headline
         */
        public function setHeadline($headline) {
            $this->headline = $headline;
        }

        /**
         * @return string
         */
        public function getHeadline() {
            return $this->headline;
        }

        /**
         * @param string $txtfontcolor
         */
        public function setTxtfontcolor($txtfontcolor) {
            $this->txtfontcolor = $txtfontcolor;
        }

        /**
         * @return string
         */
        public function getTxtfontcolor() {
            return $this->txtfontcolor;
        }

        /**
         * @param string $txtalignment
         */
        public function setTxtalignment($txtalignment) {
            $this->txtalignment = $txtalignment;
        }

        /**
         * @return string
         */
        public function getTxtalignment() {
            return $this->txtalignment;
        }

        /**
         * @param string $txtwidth
         */
        public function setTxtwidth($txtwidth) {
            $this->txtwidth = $txtwidth;
        }

        /**
         * @return string
         */
        public function getTxtwidth() {
            return $this->txtwidth;
        }

        /**
         * @param string $txt
         */
        public function setTxt($txt) {
            $this->txt = $txt;
        }

        /**
         * @return string
         */
        public function getTxt() {
            return $this->txt;
        }

        /**
         * @param boolean $completelinking
         */
        public function setCompletelinking($completelinking) {
            $this->completelinking = $completelinking;
        }

        /**
         * @return boolean
         */
        public function getCompletelinking() {
            return $this->completelinking;
        }

        /**
         * @param boolean $linkbelowcontent
         */
        public function setLinkbelowcontent($linkbelowcontent) {
            $this->linkbelowcontent = $linkbelowcontent;
        }

        /**
         * @return boolean
         */
        public function getLinkbelowcontent() {
            return $this->linkbelowcontent;
        }

        /**
         * @param boolean $linkbackgroundcolor
         */
        public function setLinkbackgroundcolor($linkbackgroundcolor) {
            $this->linkbackgroundcolor = $linkbackgroundcolor;
        }

        /**
         * @return boolean
         */
        public function getLinkbackgroundcolor() {
            return $this->linkbackgroundcolor;
        }

        /**
         * @param boolean $linkpadding
         */
        public function setLinkpadding($linkpadding) {
            $this->linkpadding = $linkpadding;
        }

        /**
         * @return boolean
         */
        public function getLinkpadding() {
            return $this->linkpadding;
        }

        /**
         * @param boolean $target
         */
        public function setTarget($target) {
            $this->target = $target;
        }

        /**
         * @return boolean
         */
        public function getTarget() {
            return $this->target;
        }

        /**
         * @param string $link
         */
        public function setLink($link) {
            $this->link = $link;
        }

        /**
         * @return string
         */
        public function getLink() {
            return $this->link;
        }

        /**
         * @param string $linkcssclass
         */
        public function setLinkcssclass($linkcssclass) {
            $this->linkcssclass = $linkcssclass;
        }

        /**
         * @return string
         */
        public function getLinkcssclass() {
            return $this->linkcssclass;
        }

        /**
         * @param boolean $linktransparent
         */
        public function setLinktransparent($linktransparent) {
            $this->linktransparent = $linktransparent;
        }

        /**
         * @return boolean
         */
        public function getLinktransparent() {
            return $this->linktransparent;
        }

        /**
         * @param string $linkbgcolor
         */
        public function setLinkbgcolor($linkbgcolor) {
            $this->linkbgcolor = $linkbgcolor;
        }

        /**
         * @return string
         */
        public function getLinkbgcolor() {
            return $this->linkbgcolor;
        }

        /**
         * @param string $linkfontcolor
         */
        public function setLinkfontcolor($linkfontcolor) {
            $this->linkfontcolor = $linkfontcolor;
        }

        /**
         * @return string
         */
        public function getLinkfontcolor() {
            return $this->linkfontcolor;
        }

        /**
         * @param string $linkbordercolor
         */
        public function setLinkbordercolor($linkbordercolor) {
            $this->linkbordercolor = $linkbordercolor;
        }

        /**
         * @return string
         */
        public function getLinkbordercolor() {
            return $this->linkbordercolor;
        }

        /**
         * @param string $linktext
         */
        public function setLinktext($linktext) {
            $this->linktext = $linktext;
        }

        /**
         * @return string
         */
        public function getLinktext() {
            return $this->linktext;
        }

        /**
         * @param string $linkalignment
         */
        public function setLinkalignment($linkalignment) {
            $this->linkalignment = $linkalignment;
        }

        /**
         * @return string
         */
        public function getLinkalignment() {
            return $this->linkalignment;
        }

        /**
         * @param string $linkwidth
         */
        public function setLinkwidth($linkwidth) {
            $this->linkwidth = $linkwidth;
        }

        /**
         * @return string
         */
        public function getLinkwidth() {
            return $this->linkwidth;
        }

        /**
         * @param \DateTime $displaydatefrom
         */
        public function setDisplaydatefrom($displaydatefrom) {
            $this->displaydatefrom = $displaydatefrom;
        }

        /**
         * @return \DateTime
         */
        public function getDisplaydatefrom() {
            return $this->displaydatefrom;
        }

        /**
         * @param \DateTime $displaydateto
         */
        public function setDisplaydateto($displaydateto) {
            $this->displaydateto = $displaydateto;
        }

        /**
         * @return \DateTime
         */
        public function getDisplaydateto() {
            return $this->displaydateto;
        }

        /**
         * @param boolean $hideinsmartphoneportrait
         */
        public function setHideinsmartphoneportrait($hideinsmartphoneportrait) {
            $this->hideinsmartphoneportrait = $hideinsmartphoneportrait;
        }

        /**
         * @return boolean
         */
        public function getHideinsmartphoneportrait() {
            return $this->hideinsmartphoneportrait;
        }

        /**
         * @param boolean $hideinsmartphonelandscape
         */
        public function setHideinsmartphonelandscape($hideinsmartphonelandscape) {
            $this->hideinsmartphonelandscape = $hideinsmartphonelandscape;
        }

        /**
         * @return boolean
         */
        public function getHideinsmartphonelandscape() {
            return $this->hideinsmartphonelandscape;
        }

        /**
         * @param boolean $hideintabletportrait
         */
        public function setHideintabletportrait($hideintabletportrait) {
            $this->hideintabletportrait = $hideintabletportrait;
        }

        /**
         * @return boolean
         */
        public function getHideintabletportrait() {
            return $this->hideintabletportrait;
        }

        /**
         * @param boolean $hideintabletlandscape
         */
        public function setHideintabletlandscape($hideintabletlandscape) {
            $this->hideintabletlandscape = $hideintabletlandscape;
        }

        /**
         * @return boolean
         */
        public function getHideintabletlandscape() {
            return $this->hideintabletlandscape;
        }

        /**
         * @param boolean $hideindesktop
         */
        public function setHideindesktop($hideindesktop) {
            $this->hideindesktop = $hideindesktop;
        }

        /**
         * @return boolean
         */
        public function getHideindesktop() {
            return $this->hideindesktop;
        }
    }