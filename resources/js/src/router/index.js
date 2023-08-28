import Vue from 'vue'
import VueRouter from 'vue-router'

// Routes
import { canNavigate } from '@/libs/acl/routeProtection'
import { isUserLoggedIn, getUserData, getHomeRouteForLoggedInUser } from '@/auth/utils'
import apps from './routes/apps'
import dashboardPro from './routes/dashboardPro'
import dashboard from './routes/dashboard'
import uiElements from './routes/ui-elements/index'
import pages from './routes/pages'
import chartsMaps from './routes/charts-maps'
import formsTable from './routes/forms-tables'
import others from './routes/others'
import master from './routes/master'
import posting from './routes/posting'
import freeCalling from './routes/free-calling'
import emprIoTrack from './routes/empr-ioTrack'
import msg91 from './routes/msg91'
import jobFeedback from './routes/job-feedback'
import analytics from './routes/analytics'
import article from './routes/article'
import forum from './routes/forum'
import fseReport from './routes/fse-report'
import executive from './routes/executive'
import requestpage from './routes/requestPage'
// import analytics from './routes/analytics';
import kmBasedEmployeecount from './routes/reports'
import jobTitleSearch from './routes/jobTitleSearch'
import employeesViewJobsCount from './routes/employees-job-view'
import adNotification from './routes/adNotification'
import teamCompetition from './routes/team-competition'
import empPlanpurchase from './routes/Employer-Plan-Purchase'
import dynamicDialogue from './routes/dynamicDialogue'
import activeJobtitlewithlivepostempcnt from './routes/active-jobtitle-live'

Vue.use(VueRouter)

const router = new VueRouter({
    mode: 'history',
    // base: process.env.BASE_URL,
    base: process.env.MIX_REQUEST_BASE_URL,
    scrollBehavior() {
        return {
            x: 0,
            y: 0
        }
    },
    routes: [
        {
            path: '/',
            redirect: { name: 'dashboard-ecommerce' }
        },
        ...apps,
        ...master,
        ...posting,
        ...executive,
        ...requestpage,
        ...freeCalling,
        ...dynamicDialogue,
        ...activeJobtitlewithlivepostempcnt,
        ...msg91,
        ...emprIoTrack,
        ...jobFeedback,
        ...dashboard,
        ...dashboardPro,
        ...pages,
        ...chartsMaps,
        ...formsTable,
        ...uiElements,
        ...others,
        ...article,
        ...forum,
        ...fseReport,
        ...analytics,
        ...kmBasedEmployeecount,
        ...jobTitleSearch,
        ...employeesViewJobsCount,
        ...adNotification,
        ...teamCompetition,
        ...empPlanpurchase,
        {
            path: '*',
            redirect: 'error-404',
        },
    ],
})

router.beforeEach((to, _, next) => {
    const isLoggedIn = isUserLoggedIn()

    if (!canNavigate(to)) {
        // Redirect to login if not logged in
        if (!isLoggedIn) return next({ name: 'auth-login' })

        // If logged in => not authorized
        return next({ name: 'misc-not-authorized' })
    }

    // Redirect if logged in
    if (to.meta.redirectIfLoggedIn && isLoggedIn) {
        const userData = getUserData()
        next(getHomeRouteForLoggedInUser(userData ? userData.role : null))
    }

    return next()
})

// ? For splash screen
// Remove afterEach hook if you are not using splash screen
router.afterEach(() => {
    // Remove initial loading
    const appLoading = document.getElementById('loading-bg')
    if (appLoading) {
        appLoading.style.display = 'none'
    }
})

export default router
