<template>
    <b-card-code title="Menu Master">
        <b-overlay
            :show="initial_loading"
            rounded="lg"
        >
            <!-- tree -->
            <v-tree
                ref="tree"
                :can-delete-root="false"
                :data="treeData"
                :draggable="true"
                :tpl="tpl"
                :halfcheck="true"
                :multiple="false"
            />
            <b-modal
                v-show="!initial_loading"
                id="main-menu-group"
                no-close-on-esc
                no-close-on-backdrop
                hide-footer
                centered
                title="Group Name"
            >
                <validation-observer ref="simpleRules">
                    <b-form>
                        <b-form-group
                            v-if="group.menuID"
                            label="Id"
                            label-for="Id"
                        >
                            <b-form-input
                                v-model="group.menuID"
                                readonly
                                placeholder="ID"
                            />
                        </b-form-group>
                        <b-form-group
                            label="Group Name"
                            label-for="Group Name"
                        >
                            <validation-provider
                                #default="{ errors }"
                                name="Group Name"
                                rules="required|min:3"
                            >
                                <b-form-input
                                    v-model="group.name"
                                    :state="errors.length > 0 ? false:null"
                                    placeholder="Group Name"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                        <b-form-group
                            label="Position"
                            label-for="Position"
                        >
                            <v-select
                                v-model="group.position"
                                :options="selectOptions(group.maxPosition)"
                                label="title"
                            />
                        </b-form-group>
                        <!-- submit button -->
                        <div class="float-right">
                            <b-col>
                                <b-button
                                    variant="outline-danger"
                                    @click="hideModal('main-menu-group')"
                                >Close
                                </b-button>

                                <b-button
                                    v-if="!group.menuID"
                                    variant="primary"
                                    type="submit"
                                    @click.prevent="validationForm"
                                >
                                    Submit
                                </b-button>
                                <b-button
                                    v-else
                                    variant="primary"
                                    type="submit"
                                    @click.prevent="validationForm"
                                >
                                    Update
                                </b-button>
                            </b-col>
                        </div>
                    </b-form>
                </validation-observer>
            </b-modal>
            <b-modal
                v-show="!initial_loading"
                id="menu-modal"
                no-close-on-esc
                no-close-on-backdrop
                hide-footer
                centered
                title="Menu Details"
            >
                <validation-observer ref="simpleRules">
                    <b-form>
                        <b-form-group
                            v-if="menu.menuID"
                            label="Id"
                            label-for="Id"
                        >
                            <b-form-input
                                v-model="menu.menuID"
                                readonly
                                placeholder="menuID"
                            />
                        </b-form-group>
                        <b-form-group>
                            <slot name="title">
                                <span style="color:red">Title *</span>
                            </slot>
                            <validation-provider
                                #default="{ errors }"
                                name="Title"
                                rules="required|min:3"
                            >
                                <b-form-input
                                    v-model="menu.title"
                                    trim
                                    :state="errors.length > 0 ? false:null"
                                    placeholder="Title"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                        <b-form-group
                            label="Route"
                            label-for="Route"
                        >
                            <validation-provider
                                #default="{ errors }"
                                name="Route"
                                rules="min:3"
                            >
                                <b-form-input
                                    v-model="menu.route"
                                    :state="errors.length > 0 ? false:null"
                                    placeholder="Route"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                        <b-form-group
                            label="Icon"
                            label-for="Icon"
                        >
                            <validation-provider
                                #default="{ errors }"
                                name="Icon"
                                rules="min:3"
                            >
                                <v-select
                                    v-model="menu.icon"
                                    :options="iconsList"
                                    label="title"
                                    :reduce="item=>item.title"
                                >
                                    <template #option="{ title, icon }">
                                        <feather-icon
                                            :icon="icon"
                                            size="16"
                                            class="align-middle mr-50"
                                        />
                                        <span> {{ title }}</span>
                                    </template>
                                </v-select>
                                <!--                            <b-form-input-->
                                <!--                                v-model="group.icon"-->
                                <!--                                :state="errors.length > 0 ? false:null"-->
                                <!--                                placeholder="Icon"-->
                                <!--                            />-->
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                        <b-form-group
                            label="Position"
                            label-for="Position"
                        >
                            <v-select
                                v-model="menu.position"
                                :options="selectOptions(menu.maxPosition)"
                                label="title"
                            />
                        </b-form-group>

                        <!-- submit button -->
                        <div class="float-right">
                            <b-col>
                                <b-button
                                    variant="outline-danger"
                                    @click="hideModal('menu-modal')"
                                >Close
                                </b-button>

                                <b-button
                                    v-if="!menu.id"
                                    variant="primary"
                                    type="submit"
                                    @click.prevent="MenuValidationForm"
                                >
                                    Submit
                                </b-button>
                                <b-button
                                    v-else
                                    variant="primary"
                                    type="submit"
                                    @click.prevent="MenuValidationForm"
                                >
                                    Update
                                </b-button>
                            </b-col>
                        </div>
                    </b-form>
                </validation-observer>
            </b-modal>
            <!--        <b-button @click="clickMethod">Add</b-button>-->
        </b-overlay>
    </b-card-code>
</template>

<script>
import { VTree } from 'vue-tree-halower'
import vSelect from 'vue-select'
import BCardCode from '@core/components/b-card-code/BCardCode.vue'
import {
    BForm, BFormGroup, BCol, BFormInput, BInputGroupPrepend, BInputGroup, BButton, VBModal, BSpinner, BOverlay,
} from 'bootstrap-vue'
import {
    required, url, alpha, alphaDash,
} from '@validations'

import { ValidationProvider, ValidationObserver } from 'vee-validate'

export default {
    components: {
        VTree,
        BCardCode,
        BForm,
        BFormGroup,
        BCol,
        BInputGroupPrepend,
        BInputGroup,
        BFormInput,
        BButton,
        VBModal,
        ValidationProvider,
        ValidationObserver,
        vSelect,
        BSpinner,
        BOverlay,
    },
    data() {
        return {
            initial_loading: true,
            iconsList: [
                {
                    title: 'Activity Icon',
                    icon: 'ActivityIcon'
                },
                {
                    title: 'Airplay Icon',
                    icon: 'AirplayIcon'
                },
                {
                    title: 'AlertCircle Icon',
                    icon: 'AlertCircleIcon'
                },
                {
                    title: 'AlertOctagon Icon',
                    icon: 'AlertOctagonIcon'
                },
                {
                    title: 'AlertTriangle Icon',
                    icon: 'AlertTriangleIcon'
                },
                {
                    title: 'AlignCenter Icon',
                    icon: 'AlignCenterIcon'
                },
                {
                    title: 'AlignJustify Icon',
                    icon: 'AlignJustifyIcon'
                },
                {
                    title: 'AlignLeft Icon',
                    icon: 'AlignLeftIcon'
                },
                {
                    title: 'AlignRight Icon',
                    icon: 'AlignRightIcon'
                },
                {
                    title: 'Anchor Icon',
                    icon: 'AnchorIcon'
                },
                {
                    title: 'Aperture Icon',
                    icon: 'ApertureIcon'
                },
                {
                    title: 'Archive Icon',
                    icon: 'ArchiveIcon'
                },
                {
                    title: 'ArrowDownCircle Icon',
                    icon: 'ArrowDownCircleIcon'
                },
                {
                    title: 'ArrowDownLeft Icon',
                    icon: 'ArrowDownLeftIcon'
                },
                {
                    title: 'ArrowDownRight Icon',
                    icon: 'ArrowDownRightIcon'
                },
                {
                    title: 'ArrowDown Icon',
                    icon: 'ArrowDownIcon'
                },
                {
                    title: 'ArrowLeftCircle Icon',
                    icon: 'ArrowLeftCircleIcon'
                },
                {
                    title: 'ArrowLeft Icon',
                    icon: 'ArrowLeftIcon'
                },
                {
                    title: 'ArrowRightCircle Icon',
                    icon: 'ArrowRightCircleIcon'
                },
                {
                    title: 'ArrowRight Icon',
                    icon: 'ArrowRightIcon'
                },
                {
                    title: 'ArrowUpCircle Icon',
                    icon: 'ArrowUpCircleIcon'
                },
                {
                    title: 'ArrowUpLeft Icon',
                    icon: 'ArrowUpLeftIcon'
                },
                {
                    title: 'ArrowUpRight Icon',
                    icon: 'ArrowUpRightIcon'
                },
                {
                    title: 'ArrowUp Icon',
                    icon: 'ArrowUpIcon'
                },
                {
                    title: 'AtSign Icon',
                    icon: 'AtSignIcon'
                },
                {
                    title: 'Award Icon',
                    icon: 'AwardIcon'
                },
                {
                    title: 'BarChart2 Icon',
                    icon: 'BarChart2Icon'
                },
                {
                    title: 'BarChart Icon',
                    icon: 'BarChartIcon'
                },
                {
                    title: 'BatteryCharging Icon',
                    icon: 'BatteryChargingIcon'
                },
                {
                    title: 'Battery Icon',
                    icon: 'BatteryIcon'
                },
                {
                    title: 'BellOff Icon',
                    icon: 'BellOffIcon'
                },
                {
                    title: 'Bell Icon',
                    icon: 'BellIcon'
                },
                {
                    title: 'Bluetooth Icon',
                    icon: 'BluetoothIcon'
                },
                {
                    title: 'Bold Icon',
                    icon: 'BoldIcon'
                },
                {
                    title: 'BookOpen Icon',
                    icon: 'BookOpenIcon'
                },
                {
                    title: 'Book Icon',
                    icon: 'BookIcon'
                },
                {
                    title: 'Bookmark Icon',
                    icon: 'BookmarkIcon'
                },
                {
                    title: 'Box Icon',
                    icon: 'BoxIcon'
                },
                {
                    title: 'Briefcase Icon',
                    icon: 'BriefcaseIcon'
                },
                {
                    title: 'Calendar Icon',
                    icon: 'CalendarIcon'
                },
                {
                    title: 'CameraOff Icon',
                    icon: 'CameraOffIcon'
                },
                {
                    title: 'Camera Icon',
                    icon: 'CameraIcon'
                },
                {
                    title: 'Cast Icon',
                    icon: 'CastIcon'
                },
                {
                    title: 'CheckCircle Icon',
                    icon: 'CheckCircleIcon'
                },
                {
                    title: 'CheckSquare Icon',
                    icon: 'CheckSquareIcon'
                },
                {
                    title: 'Check Icon',
                    icon: 'CheckIcon'
                },
                {
                    title: 'ChevronDown Icon',
                    icon: 'ChevronDownIcon'
                },
                {
                    title: 'ChevronLeft Icon',
                    icon: 'ChevronLeftIcon'
                },
                {
                    title: 'ChevronRight Icon',
                    icon: 'ChevronRightIcon'
                },
                {
                    title: 'ChevronUp Icon',
                    icon: 'ChevronUpIcon'
                },
                {
                    title: 'ChevronsDown Icon',
                    icon: 'ChevronsDownIcon'
                },
                {
                    title: 'ChevronsLeft Icon',
                    icon: 'ChevronsLeftIcon'
                },
                {
                    title: 'ChevronsRight Icon',
                    icon: 'ChevronsRightIcon'
                },
                {
                    title: 'ChevronsUp Icon',
                    icon: 'ChevronsUpIcon'
                },
                {
                    title: 'Chrome Icon',
                    icon: 'ChromeIcon'
                },
                {
                    title: 'Circle Icon',
                    icon: 'CircleIcon'
                },
                {
                    title: 'Clipboard Icon',
                    icon: 'ClipboardIcon'
                },
                {
                    title: 'Clock Icon',
                    icon: 'ClockIcon'
                },
                {
                    title: 'CloudDrizzle Icon',
                    icon: 'CloudDrizzleIcon'
                },
                {
                    title: 'CloudLightning Icon',
                    icon: 'CloudLightningIcon'
                },
                {
                    title: 'CloudOff Icon',
                    icon: 'CloudOffIcon'
                },
                {
                    title: 'CloudRain Icon',
                    icon: 'CloudRainIcon'
                },
                {
                    title: 'CloudSnow Icon',
                    icon: 'CloudSnowIcon'
                },
                {
                    title: 'Cloud Icon',
                    icon: 'CloudIcon'
                },
                {
                    title: 'Code Icon',
                    icon: 'CodeIcon'
                },
                {
                    title: 'Codepen Icon',
                    icon: 'CodepenIcon'
                },
                {
                    title: 'Codesandbox Icon',
                    icon: 'CodesandboxIcon'
                },
                {
                    title: 'Coffee Icon',
                    icon: 'CoffeeIcon'
                },
                {
                    title: 'Columns Icon',
                    icon: 'ColumnsIcon'
                },
                {
                    title: 'Command Icon',
                    icon: 'CommandIcon'
                },
                {
                    title: 'Compass Icon',
                    icon: 'CompassIcon'
                },
                {
                    title: 'Copy Icon',
                    icon: 'CopyIcon'
                },
                {
                    title: 'CornerDownLeft Icon',
                    icon: 'CornerDownLeftIcon'
                },
                {
                    title: 'CornerDownRight Icon',
                    icon: 'CornerDownRightIcon'
                },
                {
                    title: 'CornerLeftDown Icon',
                    icon: 'CornerLeftDownIcon'
                },
                {
                    title: 'CornerLeftUp Icon',
                    icon: 'CornerLeftUpIcon'
                },
                {
                    title: 'CornerRightDown Icon',
                    icon: 'CornerRightDownIcon'
                },
                {
                    title: 'CornerRightUp Icon',
                    icon: 'CornerRightUpIcon'
                },
                {
                    title: 'CornerUpLeft Icon',
                    icon: 'CornerUpLeftIcon'
                },
                {
                    title: 'CornerUpRight Icon',
                    icon: 'CornerUpRightIcon'
                },
                {
                    title: 'Cpu Icon',
                    icon: 'CpuIcon'
                },
                {
                    title: 'CreditCard Icon',
                    icon: 'CreditCardIcon'
                },
                {
                    title: 'Crop Icon',
                    icon: 'CropIcon'
                },
                {
                    title: 'Crosshair Icon',
                    icon: 'CrosshairIcon'
                },
                {
                    title: 'Database Icon',
                    icon: 'DatabaseIcon'
                },
                {
                    title: 'Delete Icon',
                    icon: 'DeleteIcon'
                },
                {
                    title: 'Disc Icon',
                    icon: 'DiscIcon'
                },
                {
                    title: 'DivideCircle Icon',
                    icon: 'DivideCircleIcon'
                },
                {
                    title: 'DivideSquare Icon',
                    icon: 'DivideSquareIcon'
                },
                {
                    title: 'Divide Icon',
                    icon: 'DivideIcon'
                },
                {
                    title: 'DollarSign Icon',
                    icon: 'DollarSignIcon'
                },
                {
                    title: 'DownloadCloud Icon',
                    icon: 'DownloadCloudIcon'
                },
                {
                    title: 'Download Icon',
                    icon: 'DownloadIcon'
                },
                {
                    title: 'Dribbble Icon',
                    icon: 'DribbbleIcon'
                },
                {
                    title: 'Droplet Icon',
                    icon: 'DropletIcon'
                },
                {
                    title: 'Edit2 Icon',
                    icon: 'Edit2Icon'
                },
                {
                    title: 'Edit3 Icon',
                    icon: 'Edit3Icon'
                },
                {
                    title: 'Edit Icon',
                    icon: 'EditIcon'
                },
                {
                    title: 'ExternalLink Icon',
                    icon: 'ExternalLinkIcon'
                },
                {
                    title: 'EyeOff Icon',
                    icon: 'EyeOffIcon'
                },
                {
                    title: 'Eye Icon',
                    icon: 'EyeIcon'
                },
                {
                    title: 'Facebook Icon',
                    icon: 'FacebookIcon'
                },
                {
                    title: 'FastForward Icon',
                    icon: 'FastForwardIcon'
                },
                {
                    title: 'Feather Icon',
                    icon: 'FeatherIcon'
                },
                {
                    title: 'Figma Icon',
                    icon: 'FigmaIcon'
                },
                {
                    title: 'FileMinus Icon',
                    icon: 'FileMinusIcon'
                },
                {
                    title: 'FilePlus Icon',
                    icon: 'FilePlusIcon'
                },
                {
                    title: 'FileText Icon',
                    icon: 'FileTextIcon'
                },
                {
                    title: 'File Icon',
                    icon: 'FileIcon'
                },
                {
                    title: 'Film Icon',
                    icon: 'FilmIcon'
                },
                {
                    title: 'Filter Icon',
                    icon: 'FilterIcon'
                },
                {
                    title: 'Flag Icon',
                    icon: 'FlagIcon'
                },
                {
                    title: 'FolderMinus Icon',
                    icon: 'FolderMinusIcon'
                },
                {
                    title: 'FolderPlus Icon',
                    icon: 'FolderPlusIcon'
                },
                {
                    title: 'Folder Icon',
                    icon: 'FolderIcon'
                },
                {
                    title: 'Framer Icon',
                    icon: 'FramerIcon'
                },
                {
                    title: 'Frown Icon',
                    icon: 'FrownIcon'
                },
                {
                    title: 'Gift Icon',
                    icon: 'GiftIcon'
                },
                {
                    title: 'GitBranch Icon',
                    icon: 'GitBranchIcon'
                },
                {
                    title: 'GitCommit Icon',
                    icon: 'GitCommitIcon'
                },
                {
                    title: 'GitMerge Icon',
                    icon: 'GitMergeIcon'
                },
                {
                    title: 'GitPullRequest Icon',
                    icon: 'GitPullRequestIcon'
                },
                {
                    title: 'Github Icon',
                    icon: 'GithubIcon'
                },
                {
                    title: 'Gitlab Icon',
                    icon: 'GitlabIcon'
                },
                {
                    title: 'Globe Icon',
                    icon: 'GlobeIcon'
                },
                {
                    title: 'Grid Icon',
                    icon: 'GridIcon'
                },
                {
                    title: 'HardDrive Icon',
                    icon: 'HardDriveIcon'
                },
                {
                    title: 'Hash Icon',
                    icon: 'HashIcon'
                },
                {
                    title: 'Headphones Icon',
                    icon: 'HeadphonesIcon'
                },
                {
                    title: 'Heart Icon',
                    icon: 'HeartIcon'
                },
                {
                    title: 'HelpCircle Icon',
                    icon: 'HelpCircleIcon'
                },
                {
                    title: 'Hexagon Icon',
                    icon: 'HexagonIcon'
                },
                {
                    title: 'Home Icon',
                    icon: 'HomeIcon'
                },
                {
                    title: 'Image Icon',
                    icon: 'ImageIcon'
                },
                {
                    title: 'Inbox Icon',
                    icon: 'InboxIcon'
                },
                {
                    title: 'Info Icon',
                    icon: 'InfoIcon'
                },
                {
                    title: 'Instagram Icon',
                    icon: 'InstagramIcon'
                },
                {
                    title: 'Italic Icon',
                    icon: 'ItalicIcon'
                },
                {
                    title: 'Key Icon',
                    icon: 'KeyIcon'
                },
                {
                    title: 'Layers Icon',
                    icon: 'LayersIcon'
                },
                {
                    title: 'Layout Icon',
                    icon: 'LayoutIcon'
                },
                {
                    title: 'LifeBuoy Icon',
                    icon: 'LifeBuoyIcon'
                },
                {
                    title: 'Link2 Icon',
                    icon: 'Link2Icon'
                },
                {
                    title: 'Link Icon',
                    icon: 'LinkIcon'
                },
                {
                    title: 'Linkedin Icon',
                    icon: 'LinkedinIcon'
                },
                {
                    title: 'List Icon',
                    icon: 'ListIcon'
                },
                {
                    title: 'Loader Icon',
                    icon: 'LoaderIcon'
                },
                {
                    title: 'Lock Icon',
                    icon: 'LockIcon'
                },
                {
                    title: 'LogIn Icon',
                    icon: 'LogInIcon'
                },
                {
                    title: 'LogOut Icon',
                    icon: 'LogOutIcon'
                },
                {
                    title: 'Mail Icon',
                    icon: 'MailIcon'
                },
                {
                    title: 'MapPin Icon',
                    icon: 'MapPinIcon'
                },
                {
                    title: 'Map Icon',
                    icon: 'MapIcon'
                },
                {
                    title: 'Maximize2 Icon',
                    icon: 'Maximize2Icon'
                },
                {
                    title: 'Maximize Icon',
                    icon: 'MaximizeIcon'
                },
                {
                    title: 'Meh Icon',
                    icon: 'MehIcon'
                },
                {
                    title: 'Menu Icon',
                    icon: 'MenuIcon'
                },
                {
                    title: 'MessageCircle Icon',
                    icon: 'MessageCircleIcon'
                },
                {
                    title: 'MessageSquare Icon',
                    icon: 'MessageSquareIcon'
                },
                {
                    title: 'MicOff Icon',
                    icon: 'MicOffIcon'
                },
                {
                    title: 'Mic Icon',
                    icon: 'MicIcon'
                },
                {
                    title: 'Minimize2 Icon',
                    icon: 'Minimize2Icon'
                },
                {
                    title: 'Minimize Icon',
                    icon: 'MinimizeIcon'
                },
                {
                    title: 'MinusCircle Icon',
                    icon: 'MinusCircleIcon'
                },
                {
                    title: 'MinusSquare Icon',
                    icon: 'MinusSquareIcon'
                },
                {
                    title: 'Minus Icon',
                    icon: 'MinusIcon'
                },
                {
                    title: 'Monitor Icon',
                    icon: 'MonitorIcon'
                },
                {
                    title: 'Moon Icon',
                    icon: 'MoonIcon'
                },
                {
                    title: 'MoreHorizontal Icon',
                    icon: 'MoreHorizontalIcon'
                },
                {
                    title: 'MoreVertical Icon',
                    icon: 'MoreVerticalIcon'
                },
                {
                    title: 'MousePointer Icon',
                    icon: 'MousePointerIcon'
                },
                {
                    title: 'Move Icon',
                    icon: 'MoveIcon'
                },
                {
                    title: 'Music Icon',
                    icon: 'MusicIcon'
                },
                {
                    title: 'Navigation2 Icon',
                    icon: 'Navigation2Icon'
                },
                {
                    title: 'Navigation Icon',
                    icon: 'NavigationIcon'
                },
                {
                    title: 'Octagon Icon',
                    icon: 'OctagonIcon'
                },
                {
                    title: 'Package Icon',
                    icon: 'PackageIcon'
                },
                {
                    title: 'Paperclip Icon',
                    icon: 'PaperclipIcon'
                },
                {
                    title: 'PauseCircle Icon',
                    icon: 'PauseCircleIcon'
                },
                {
                    title: 'Pause Icon',
                    icon: 'PauseIcon'
                },
                {
                    title: 'PenTool Icon',
                    icon: 'PenToolIcon'
                },
                {
                    title: 'Percent Icon',
                    icon: 'PercentIcon'
                },
                {
                    title: 'PhoneCall Icon',
                    icon: 'PhoneCallIcon'
                },
                {
                    title: 'PhoneForwarded Icon',
                    icon: 'PhoneForwardedIcon'
                },
                {
                    title: 'PhoneIncoming Icon',
                    icon: 'PhoneIncomingIcon'
                },
                {
                    title: 'PhoneMissed Icon',
                    icon: 'PhoneMissedIcon'
                },
                {
                    title: 'PhoneOff Icon',
                    icon: 'PhoneOffIcon'
                },
                {
                    title: 'PhoneOutgoing Icon',
                    icon: 'PhoneOutgoingIcon'
                },
                {
                    title: 'Phone Icon',
                    icon: 'PhoneIcon'
                },
                {
                    title: 'PieChart Icon',
                    icon: 'PieChartIcon'
                },
                {
                    title: 'PlayCircle Icon',
                    icon: 'PlayCircleIcon'
                },
                {
                    title: 'Play Icon',
                    icon: 'PlayIcon'
                },
                {
                    title: 'PlusCircle Icon',
                    icon: 'PlusCircleIcon'
                },
                {
                    title: 'PlusSquare Icon',
                    icon: 'PlusSquareIcon'
                },
                {
                    title: 'Plus Icon',
                    icon: 'PlusIcon'
                },
                {
                    title: 'Pocket Icon',
                    icon: 'PocketIcon'
                },
                {
                    title: 'Power Icon',
                    icon: 'PowerIcon'
                },
                {
                    title: 'Printer Icon',
                    icon: 'PrinterIcon'
                },
                {
                    title: 'Radio Icon',
                    icon: 'RadioIcon'
                },
                {
                    title: 'RefreshCcw Icon',
                    icon: 'RefreshCcwIcon'
                },
                {
                    title: 'RefreshCw Icon',
                    icon: 'RefreshCwIcon'
                },
                {
                    title: 'Repeat Icon',
                    icon: 'RepeatIcon'
                },
                {
                    title: 'Rewind Icon',
                    icon: 'RewindIcon'
                },
                {
                    title: 'RotateCcw Icon',
                    icon: 'RotateCcwIcon'
                },
                {
                    title: 'RotateCw Icon',
                    icon: 'RotateCwIcon'
                },
                {
                    title: 'Rss Icon',
                    icon: 'RssIcon'
                },
                {
                    title: 'Save Icon',
                    icon: 'SaveIcon'
                },
                {
                    title: 'Scissors Icon',
                    icon: 'ScissorsIcon'
                },
                {
                    title: 'Search Icon',
                    icon: 'SearchIcon'
                },
                {
                    title: 'Send Icon',
                    icon: 'SendIcon'
                },
                {
                    title: 'Server Icon',
                    icon: 'ServerIcon'
                },
                {
                    title: 'Settings Icon',
                    icon: 'SettingsIcon'
                },
                {
                    title: 'Share2 Icon',
                    icon: 'Share2Icon'
                },
                {
                    title: 'Share Icon',
                    icon: 'ShareIcon'
                },
                {
                    title: 'ShieldOff Icon',
                    icon: 'ShieldOffIcon'
                },
                {
                    title: 'Shield Icon',
                    icon: 'ShieldIcon'
                },
                {
                    title: 'ShoppingBag Icon',
                    icon: 'ShoppingBagIcon'
                },
                {
                    title: 'ShoppingCart Icon',
                    icon: 'ShoppingCartIcon'
                },
                {
                    title: 'Shuffle Icon',
                    icon: 'ShuffleIcon'
                },
                {
                    title: 'Sidebar Icon',
                    icon: 'SidebarIcon'
                },
                {
                    title: 'SkipBack Icon',
                    icon: 'SkipBackIcon'
                },
                {
                    title: 'SkipForward Icon',
                    icon: 'SkipForwardIcon'
                },
                {
                    title: 'Slack Icon',
                    icon: 'SlackIcon'
                },
                {
                    title: 'Slash Icon',
                    icon: 'SlashIcon'
                },
                {
                    title: 'Sliders Icon',
                    icon: 'SlidersIcon'
                },
                {
                    title: 'Smartphone Icon',
                    icon: 'SmartphoneIcon'
                },
                {
                    title: 'Smile Icon',
                    icon: 'SmileIcon'
                },
                {
                    title: 'Speaker Icon',
                    icon: 'SpeakerIcon'
                },
                {
                    title: 'Square Icon',
                    icon: 'SquareIcon'
                },
                {
                    title: 'Star Icon',
                    icon: 'StarIcon'
                },
                {
                    title: 'StopCircle Icon',
                    icon: 'StopCircleIcon'
                },
                {
                    title: 'Sun Icon',
                    icon: 'SunIcon'
                },
                {
                    title: 'Sunrise Icon',
                    icon: 'SunriseIcon'
                },
                {
                    title: 'Sunset Icon',
                    icon: 'SunsetIcon'
                },
                {
                    title: 'Tablet Icon',
                    icon: 'TabletIcon'
                },
                {
                    title: 'Tag Icon',
                    icon: 'TagIcon'
                },
                {
                    title: 'Target Icon',
                    icon: 'TargetIcon'
                },
                {
                    title: 'Terminal Icon',
                    icon: 'TerminalIcon'
                },
                {
                    title: 'Thermometer Icon',
                    icon: 'ThermometerIcon'
                },
                {
                    title: 'ThumbsDown Icon',
                    icon: 'ThumbsDownIcon'
                },
                {
                    title: 'ThumbsUp Icon',
                    icon: 'ThumbsUpIcon'
                },
                {
                    title: 'ToggleLeft Icon',
                    icon: 'ToggleLeftIcon'
                },
                {
                    title: 'ToggleRight Icon',
                    icon: 'ToggleRightIcon'
                },
                {
                    title: 'Tool Icon',
                    icon: 'ToolIcon'
                },
                {
                    title: 'Trash2 Icon',
                    icon: 'Trash2Icon'
                },
                {
                    title: 'Trash Icon',
                    icon: 'TrashIcon'
                },
                {
                    title: 'Trello Icon',
                    icon: 'TrelloIcon'
                },
                {
                    title: 'TrendingDown Icon',
                    icon: 'TrendingDownIcon'
                },
                {
                    title: 'TrendingUp Icon',
                    icon: 'TrendingUpIcon'
                },
                {
                    title: 'Triangle Icon',
                    icon: 'TriangleIcon'
                },
                {
                    title: 'Truck Icon',
                    icon: 'TruckIcon'
                },
                {
                    title: 'Tv Icon',
                    icon: 'TvIcon'
                },
                {
                    title: 'Twitch Icon',
                    icon: 'TwitchIcon'
                },
                {
                    title: 'Twitter Icon',
                    icon: 'TwitterIcon'
                },
                {
                    title: 'Type Icon',
                    icon: 'TypeIcon'
                },
                {
                    title: 'Umbrella Icon',
                    icon: 'UmbrellaIcon'
                },
                {
                    title: 'Underline Icon',
                    icon: 'UnderlineIcon'
                },
                {
                    title: 'Unlock Icon',
                    icon: 'UnlockIcon'
                },
                {
                    title: 'UploadCloud Icon',
                    icon: 'UploadCloudIcon'
                },
                {
                    title: 'Upload Icon',
                    icon: 'UploadIcon'
                },
                {
                    title: 'UserCheck Icon',
                    icon: 'UserCheckIcon'
                },
                {
                    title: 'UserMinus Icon',
                    icon: 'UserMinusIcon'
                },
                {
                    title: 'UserPlus Icon',
                    icon: 'UserPlusIcon'
                },
                {
                    title: 'UserX Icon',
                    icon: 'UserXIcon'
                },
                {
                    title: 'User Icon',
                    icon: 'UserIcon'
                },
                {
                    title: 'Users Icon',
                    icon: 'UsersIcon'
                },
                {
                    title: 'VideoOff Icon',
                    icon: 'VideoOffIcon'
                },
                {
                    title: 'Video Icon',
                    icon: 'VideoIcon'
                },
                {
                    title: 'Voicemail Icon',
                    icon: 'VoicemailIcon'
                },
                {
                    title: 'Volume1 Icon',
                    icon: 'Volume1Icon'
                },
                {
                    title: 'Volume2 Icon',
                    icon: 'Volume2Icon'
                },
                {
                    title: 'VolumeX Icon',
                    icon: 'VolumeXIcon'
                },
                {
                    title: 'Volume Icon',
                    icon: 'VolumeIcon'
                },
                {
                    title: 'Watch Icon',
                    icon: 'WatchIcon'
                },
                {
                    title: 'WifiOff Icon',
                    icon: 'WifiOffIcon'
                },
                {
                    title: 'Wifi Icon',
                    icon: 'WifiIcon'
                },
                {
                    title: 'Wind Icon',
                    icon: 'WindIcon'
                },
                {
                    title: 'XCircle Icon',
                    icon: 'XCircleIcon'
                },
                {
                    title: 'XOctagon Icon',
                    icon: 'XOctagonIcon'
                },
                {
                    title: 'XSquare Icon',
                    icon: 'XSquareIcon'
                },
                {
                    title: 'X Icon',
                    icon: 'XIcon'
                },
                {
                    title: 'Youtube Icon',
                    icon: 'YoutubeIcon'
                },
                {
                    title: 'ZapOff Icon',
                    icon: 'ZapOffIcon'
                },
                {
                    title: 'Zap Icon',
                    icon: 'ZapIcon'
                },
                {
                    title: 'ZoomIn Icon',
                    icon: 'ZoomInIcon'
                },
                {
                    title: 'ZoomOut Icon',
                    icon: 'ZoomOutIcon'
                },
            ],
            group: {
                id: '',
                menuID: '',
                name: '',
                position: '',
                maxPosition: '',
            },
            menu: {
                groupID: '',
                menuID: '',
                id: '',
                title: '',
                route: '',
                icon: '',
                position: '',
                maxPosition: '',
            },
            searchword: '',
            treeData: [
                {
                    title: 'Menu',
                    name: '0',
                    expanded: true,
                    children: [
                        // {
                        //     title: 'node 1-1',
                        //     name: 'node2',
                        //     expanded: true,
                        //     children: [
                        //         {
                        //             title: 'node 1-1-1',
                        //             name: 'node3',
                        //         }, {
                        //             title: 'node 1-1-2',
                        //             name: 'node4',
                        //         }, {
                        //             title: 'node 1-1-3',
                        //             name: 'node5',
                        //         },
                        //     ],
                        // },
                        // {
                        //     title: 'node 1-2',
                        //     name: 'node6',
                        //     expanded: true,
                        //     children: [
                        //         {
                        //             title: '<span style=\'color: red\'>node 1-2-1</span>',
                        //             name: 'node7',
                        //         }, {
                        //             title: '<span style=\'color: red\'>node 1-2-2</span>',
                        //             name: 'node8',
                        //         },
                        //     ],
                        // },
                    ],
                },
            ],
        }
    },
    created() {
        this.menuListing()
    },
    methods: {
        selectOptions(value) {
            const a = []
            for (let i = 1; i <= value; i++) {
                a.push(i)
            }
            return a
        },
        hideModal(modalID) {
            this.$bvModal.hide(modalID)
        },
        addMethod(node, parent, index, edit = 0) {
            if (!parent) {
                parent = {
                    name: 0,
                    children: [],
                }
            }
            if (edit) {
                if (parent.name == 0) {
                    this.group.maxPosition = Object.keys(parent.children).length
                    this.group.menuID = node && node.name
                    this.group.name = node && node.title
                    this.group.position = node && node.position
                    if (this.group.menuID != 0) this.$bvModal.show('main-menu-group')
                    return
                }
                this.menu = node
                this.menu.menuID = node && node.name
                this.menu.groupID = parent && parent.name
                this.$bvModal.show('menu-modal')
                this.menu.maxPosition = parent.children && Object.keys(parent.children).length
            } else if (node.depth == 3) {
                alert('Cant add child')
            } else if (node.name == 0) {
                this.group = {
                    menuID: '',
                    id: '',
                    name: '',
                }
                this.group.maxPosition = this.group.position = Object.keys(node.children).length + 1
                this.$bvModal.show('main-menu-group')
            } else {
                this.menu = {
                    groupID: '',
                    menuID: '',
                    id: '',
                    title: '',
                    route: '',
                    icon: '',
                    position: '',
                    maxPosition: '',
                }
                this.menu.groupID = node.name
                let childrenCount = 0
                if (node.children) {
                    childrenCount = Object.keys(node.children).length
                }
                this.menu.maxPosition = this.menu.position = childrenCount + 1
                // alert(this.menu.position)
                this.$bvModal.show('menu-modal')
            }
        },
        async deleteMethod(node, parent, index) {
            this.initial_loading = true
            console.log(node)
            if (node.children) {
                this.initial_loading = false
                alert('Cannot Delete Parent')
                return
            }
            this.$swal({
                title: 'Are you sure?',
                text: 'You won\'t be able to revert this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Delete',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ml-1',
                },
                buttonsStyling: false,
            })
                .then(async result => {
                    if (result.value) {
                        this.initial_loading = true
                        const callAxios = await this.callAxios('/deleteMenuList', { id: node.name }, 'post')
                        if (callAxios.status === 200) {
                            this.sweetAlertmethod('success', 'Success', 'Successfully Deleted', 'btn-primary')
                            // this.toast("Success", "Successfully created", 'success');
                            // this.total = callAxios.data.length;
                            // this.rows = callAxios.data;
                            // if (callAxios.data.id) {
                            //     this.treeData[0].children.push(
                            //         {
                            //             title: this.group.name,
                            //             name: callAxios.data.id
                            //         }
                            //     )
                            // }
                            this.menuListing()
                            // console.log(callAxios.data);
                        } else {
                            this.initial_loading = false
                            // this.toast("Error", callAxios.response.data.message, 'danger');
                            this.sweetAlertmethod('error', 'Error', callAxios.response.data.message, 'btn-primary')
                        }
                    } else {
                        console.log('2')
                    }
                })
            console.log(parent)
            console.log(index)
            this.initial_loading = false
            // this.$refs.tree.delNode(node, parent, index)
        },
        tpl(...args) {
            let nodeLength = 1
            const {
                0: node,
                2: parent,
                3: index
            } = args
            let addBtnstyle = 'inline-block'
            let titleClass = node.selected ? 'node-title node-selected' : 'node-title'
            if (node.searched) titleClass += ' node-searched'
            nodeLength = node.name.toString()
                .split('-').length
            if (nodeLength == 3) addBtnstyle = 'none'
            return <span>
        <span class={titleClass} domPropsInnerHTML={node.title} onClick={() => {
            this.addMethod(node, parent, index, 1)
            this.$refs.tree.nodeSelected(node)
        }}
        ></span>
        <button class="btn-async text-primary border-0 cursor-pointer mr-1"
                onClick={() => this.addMethod(node, parent, index)}
        >Add</button>
        <button class="btn-delete text-danger border-0 cursor-pointer"
                onClick={() => this.deleteMethod(node, parent, index)}
        >Delete</button>
      </span>
        },
        // <span class={titleClass} domPropsInnerHTML={node.title} onClick={() => {
        //     this.$refs.tree.nodeSelected(node)
        // }}></span>
        // <span class="tree-expand" onClick={() => this.$refs.tree.addNode(node, {title: 'sync node'})}>+</span>
        //         name:'node16',
        // <button className="btn-delete text-danger border-0 cursor-pointer"
        //         onClick={() => this.$refs.tree.delNode(node, parent, index)}>delete</button>
        // <button className="btn-async text-warning border-0 cursor-pointer mr-1"
        //         onClick={() => this.asyncLoad(node)}>async</button>
        async asyncLoad(node) {
            const { checked = false } = node
            this.$set(node, 'loading', true)
            const pro = new Promise(resolve => {
                setTimeout(resolve, 2000, ['async node1', 'async node2'])
            })
            this.$refs.tree.addNodes(node, await pro)
            this.$set(node, 'loading', false)
            if (checked) {
                this.$refs.tree.childCheckedHandle(node, checked)
            }
        },
        validationForm() {
            this.$refs.simpleRules.validate()
                .then(async success => {
                    if (success) {
                        this.$bvModal.hide('main-menu-group')
                        this.initial_loading = true
                        const callAxios = await this.callAxios('/saveMenuList', this.group, 'post')
                        const desc = this.group.menuID ? 'Successfully Updated' : 'Successfully created'
                        if (callAxios.status === 200) {
                            this.sweetAlertmethod('success', 'Success', desc, 'btn-primary')
                            // this.toast("Success", "Successfully created", 'success');
                            // this.total = callAxios.data.length;
                            // this.rows = callAxios.data;
                            // if (callAxios.data.id) {
                            //     this.treeData[0].children.push(
                            //         {
                            //             title: this.group.name,
                            //             name: callAxios.data.id
                            //         }
                            //     )
                            // }
                            this.$bvModal.hide('main-menu-group')
                            this.group = {
                                menuID: '',
                                name: '',
                            }
                            this.menuListing()
                            // console.log(callAxios.data);
                        } else {
                            // this.toast("Error", callAxios.response.data.message, 'danger');
                            this.$bvModal.show('main-menu-group')
                            this.sweetAlertmethod('error', 'Error', callAxios.response.data.message, 'btn-primary')
                        }
                        this.initial_loading = false
                    }
                })
        },
        MenuValidationForm() {
            this.$refs.simpleRules.validate()
                .then(async success => {
                    if (success) {
                        this.$bvModal.hide('menu-modal')
                        this.initial_loading = true
                        const callAxios = await this.callAxios('/saveMenuList', this.menu, 'post')
                        const desc = this.menu.menuID ? 'Successfully Updated' : 'Successfully created'
                        if (callAxios.status === 200) {
                            this.sweetAlertmethod('success', 'Success', desc, 'btn-primary')
                            this.getMenuBarToShow()
                            // this.toast("Success", "Successfully created", 'success');
                            // this.total = callAxios.data.length;
                            // this.rows = callAxios.data;
                            // if (callAxios.data.id) {
                            //     this.treeData[0].children.push(
                            //         {
                            //             title: this.group.name,
                            //             name: callAxios.data.id
                            //         }
                            //     )
                            // }
                            this.$bvModal.hide('menu-modal')
                            this.menu = {
                                groupID: '',
                                menuID: '',
                                id: '',
                                title: '',
                                route: '',
                                icon: '',
                            }
                            this.menuListing()
                            // console.log(callAxios.data);
                        } else {
                            this.$bvModal.show('menu-modal')
                            // this.toast("Error", callAxios.response.data.message, 'danger');
                            this.sweetAlertmethod('error', 'Error', callAxios.response.data.message, 'btn-primary')
                        }
                        this.initial_loading = false
                    }
                })
        },
        async menuListing() {
            // alert();
            const callAxios = await this.callAxios('/showMenuGroup', '', 'post')
            if (callAxios.status === 200) {
                if (callAxios.data) {
                    this.treeData[0].children = callAxios.data
                }
            } else {
                this.sweetAlertmethod('error', 'Error', callAxios.response.data.message, 'btn-primary')
            }
            this.initial_loading = false
        },
    },
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
@import "@resources/scss/vue/libs/tree.scss";
</style>
