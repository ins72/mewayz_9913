<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WorkspaceSetupController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('pages.index');
})->name('home');

// Dashboard route
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Demo dashboard without authentication
Route::get('/demo', function () {
    return view('demo-dashboard');
})->name('demo');

// Include legal routes
require __DIR__.'/legal.php';

// Include auth routes
require __DIR__.'/auth.php';

// Include business routes
require __DIR__.'/business.php';

// Include install routes
require __DIR__.'/install.php';

Route::get('/landing', function () {
    return view('pages.landing');
});

// Workspace Setup Routes
Route::middleware('auth')->group(function () {
    Route::get('/workspace-setup', [WorkspaceSetupController::class, 'index'])->name('workspace-setup.index');
    Route::post('/api/workspace-setup/step-1', [WorkspaceSetupController::class, 'processStep1'])->name('workspace-setup.step-1');
    Route::post('/api/workspace-setup/step-2', [WorkspaceSetupController::class, 'processStep2'])->name('workspace-setup.step-2');
    Route::post('/api/workspace-setup/step-3', [WorkspaceSetupController::class, 'processStep3'])->name('workspace-setup.step-3');
    Route::post('/api/workspace-setup/step-4', [WorkspaceSetupController::class, 'processStep4'])->name('workspace-setup.step-4');
    Route::post('/api/workspace-setup/step-5', [WorkspaceSetupController::class, 'processStep5'])->name('workspace-setup.step-5');
    Route::post('/api/workspace-setup/available-features', [WorkspaceSetupController::class, 'getAvailableFeatures'])->name('workspace-setup.available-features');
    Route::post('/api/workspace-setup/calculate-pricing', [WorkspaceSetupController::class, 'calculatePricing'])->name('workspace-setup.calculate-pricing');
});

// Test route without auth
Route::get('/test-dashboard', function () {
    return view('pages.dashboard.index');
})->name('test-dashboard');

// Dashboard Routes (protected by auth middleware)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('pages.dashboard.index');
    })->name('dashboard');

    Route::get('/dashboard/workspace', function () {
        return view('pages.dashboard.workspace.index');
    })->name('dashboard-workspace-index');

    Route::get('/dashboard/sites', function () {
        return view('pages.dashboard.sites.index');
    })->name('dashboard-sites-index');

    Route::get('/dashboard/templates', function () {
        return view('pages.dashboard.templates.index');
    })->name('dashboard-templates-index');

    Route::get('/dashboard/audience', function () {
        return view('pages.dashboard.audience.index');
    })->name('dashboard-audience-index');

    Route::get('/dashboard/community', function () {
        return view('pages.dashboard.community.index');
    })->name('dashboard-community-index');

    Route::get('/dashboard/booking', function () {
        return view('pages.dashboard.booking.index');
    })->name('dashboard-booking-index');

    Route::get('/dashboard/automation', function () {
        return view('pages.dashboard.automation.index');
    })->name('dashboard-automation-index');

    Route::get('/dashboard/analytics', function () {
        return view('pages.dashboard.analytics.index');
    })->name('dashboard-analytics-index');

    Route::get('/dashboard/settings', function () {
        return view('pages.dashboard.settings.index');
    })->name('dashboard-settings-index');

    Route::get('/dashboard/account', function () {
        return view('pages.dashboard.account.index');
    })->name('dashboard-account-index');

    Route::get('/dashboard/upgrade', function () {
        return view('pages.dashboard.upgrade.index');
    })->name('dashboard-upgrade-index');

    Route::get('/dashboard/support', function () {
        return view('pages.dashboard.support.index');
    })->name('dashboard-support-index');

    Route::get('/dashboard/admin', function () {
        return view('pages.dashboard.admin.index');
    })->name('dashboard-admin-index');

    Route::get('/dashboard/help', function () {
        return view('pages.dashboard.help.index');
    })->name('dashboard-help-index');

    Route::get('/dashboard/feedback', function () {
        return view('pages.dashboard.feedback.index');
    })->name('dashboard-feedback-index');

    Route::get('/dashboard/logout', function () {
        return view('pages.dashboard.logout.index');
    })->name('dashboard-logout-index');

    Route::get('/dashboard/profile', function () {
        return view('pages.dashboard.profile.index');
    })->name('dashboard-profile-index');

    Route::get('/dashboard/billing', function () {
        return view('pages.dashboard.billing.index');
    })->name('dashboard-billing-index');

    Route::get('/dashboard/notifications', function () {
        return view('pages.dashboard.notifications.index');
    })->name('dashboard-notifications-index');

    Route::get('/dashboard/security', function () {
        return view('pages.dashboard.security.index');
    })->name('dashboard-security-index');

    Route::get('/dashboard/integrations', function () {
        return view('pages.dashboard.integrations.index');
    })->name('dashboard-integrations-index');

    Route::get('/dashboard/api', function () {
        return view('pages.dashboard.api.index');
    })->name('dashboard-api-index');

    Route::get('/dashboard/team', function () {
        return view('pages.dashboard.team.index');
    })->name('dashboard-team-index');

    Route::get('/dashboard/reports', function () {
        return view('pages.dashboard.reports.index');
    })->name('dashboard-reports-index');

    Route::get('/dashboard/exports', function () {
        return view('pages.dashboard.exports.index');
    })->name('dashboard-exports-index');

    Route::get('/dashboard/backup', function () {
        return view('pages.dashboard.backup.index');
    })->name('dashboard-backup-index');

    Route::get('/dashboard/maintenance', function () {
        return view('pages.dashboard.maintenance.index');
    })->name('dashboard-maintenance-index');

    Route::get('/dashboard/logs', function () {
        return view('pages.dashboard.logs.index');
    })->name('dashboard-logs-index');

    Route::get('/dashboard/performance', function () {
        return view('pages.dashboard.performance.index');
    })->name('dashboard-performance-index');

    Route::get('/dashboard/monitoring', function () {
        return view('pages.dashboard.monitoring.index');
    })->name('dashboard-monitoring-index');

    Route::get('/dashboard/health', function () {
        return view('pages.dashboard.health.index');
    })->name('dashboard-health-index');

    Route::get('/dashboard/status', function () {
        return view('pages.dashboard.status.index');
    })->name('dashboard-status-index');

    Route::get('/dashboard/debug', function () {
        return view('pages.dashboard.debug.index');
    })->name('dashboard-debug-index');

    Route::get('/dashboard/tools', function () {
        return view('pages.dashboard.tools.index');
    })->name('dashboard-tools-index');

    Route::get('/dashboard/utilities', function () {
        return view('pages.dashboard.utilities.index');
    })->name('dashboard-utilities-index');

    Route::get('/dashboard/migration', function () {
        return view('pages.dashboard.migration.index');
    })->name('dashboard-migration-index');

    Route::get('/dashboard/import', function () {
        return view('pages.dashboard.import.index');
    })->name('dashboard-import-index');

    Route::get('/dashboard/export', function () {
        return view('pages.dashboard.export.index');
    })->name('dashboard-export-index');

    Route::get('/dashboard/cache', function () {
        return view('pages.dashboard.cache.index');
    })->name('dashboard-cache-index');

    Route::get('/dashboard/queue', function () {
        return view('pages.dashboard.queue.index');
    })->name('dashboard-queue-index');

    Route::get('/dashboard/schedule', function () {
        return view('pages.dashboard.schedule.index');
    })->name('dashboard-schedule-index');

    Route::get('/dashboard/jobs', function () {
        return view('pages.dashboard.jobs.index');
    })->name('dashboard-jobs-index');

    Route::get('/dashboard/workers', function () {
        return view('pages.dashboard.workers.index');
    })->name('dashboard-workers-index');

    Route::get('/dashboard/horizon', function () {
        return view('pages.dashboard.horizon.index');
    })->name('dashboard-horizon-index');

    Route::get('/dashboard/telescope', function () {
        return view('pages.dashboard.telescope.index');
    })->name('dashboard-telescope-index');

    Route::get('/dashboard/nova', function () {
        return view('pages.dashboard.nova.index');
    })->name('dashboard-nova-index');

    Route::get('/dashboard/forge', function () {
        return view('pages.dashboard.forge.index');
    })->name('dashboard-forge-index');

    Route::get('/dashboard/vapor', function () {
        return view('pages.dashboard.vapor.index');
    })->name('dashboard-vapor-index');

    Route::get('/dashboard/envoyer', function () {
        return view('pages.dashboard.envoyer.index');
    })->name('dashboard-envoyer-index');

    Route::get('/dashboard/spark', function () {
        return view('pages.dashboard.spark.index');
    })->name('dashboard-spark-index');

    Route::get('/dashboard/cashier', function () {
        return view('pages.dashboard.cashier.index');
    })->name('dashboard-cashier-index');

    Route::get('/dashboard/passport', function () {
        return view('pages.dashboard.passport.index');
    })->name('dashboard-passport-index');

    Route::get('/dashboard/sanctum', function () {
        return view('pages.dashboard.sanctum.index');
    })->name('dashboard-sanctum-index');

    Route::get('/dashboard/scout', function () {
        return view('pages.dashboard.scout.index');
    })->name('dashboard-scout-index');

    Route::get('/dashboard/socialite', function () {
        return view('pages.dashboard.socialite.index');
    })->name('dashboard-socialite-index');

    Route::get('/dashboard/dusk', function () {
        return view('pages.dashboard.dusk.index');
    })->name('dashboard-dusk-index');

    Route::get('/dashboard/homestead', function () {
        return view('pages.dashboard.homestead.index');
    })->name('dashboard-homestead-index');

    Route::get('/dashboard/valet', function () {
        return view('pages.dashboard.valet.index');
    })->name('dashboard-valet-index');

    Route::get('/dashboard/mix', function () {
        return view('pages.dashboard.mix.index');
    })->name('dashboard-mix-index');

    Route::get('/dashboard/echo', function () {
        return view('pages.dashboard.echo.index');
    })->name('dashboard-echo-index');

    Route::get('/dashboard/pusher', function () {
        return view('pages.dashboard.pusher.index');
    })->name('dashboard-pusher-index');

    Route::get('/dashboard/redis', function () {
        return view('pages.dashboard.redis.index');
    })->name('dashboard-redis-index');

    Route::get('/dashboard/memcached', function () {
        return view('pages.dashboard.memcached.index');
    })->name('dashboard-memcached-index');

    Route::get('/dashboard/database', function () {
        return view('pages.dashboard.database.index');
    })->name('dashboard-database-index');

    Route::get('/dashboard/elasticsearch', function () {
        return view('pages.dashboard.elasticsearch.index');
    })->name('dashboard-elasticsearch-index');

    Route::get('/dashboard/algolia', function () {
        return view('pages.dashboard.algolia.index');
    })->name('dashboard-algolia-index');

    Route::get('/dashboard/meilisearch', function () {
        return view('pages.dashboard.meilisearch.index');
    })->name('dashboard-meilisearch-index');

    Route::get('/dashboard/typesense', function () {
        return view('pages.dashboard.typesense.index');
    })->name('dashboard-typesense-index');

    Route::get('/dashboard/solr', function () {
        return view('pages.dashboard.solr.index');
    })->name('dashboard-solr-index');

    Route::get('/dashboard/sphinx', function () {
        return view('pages.dashboard.sphinx.index');
    })->name('dashboard-sphinx-index');

    Route::get('/dashboard/whoosh', function () {
        return view('pages.dashboard.whoosh.index');
    })->name('dashboard-whoosh-index');

    Route::get('/dashboard/xapian', function () {
        return view('pages.dashboard.xapian.index');
    })->name('dashboard-xapian-index');

    Route::get('/dashboard/lucene', function () {
        return view('pages.dashboard.lucene.index');
    })->name('dashboard-lucene-index');

    Route::get('/dashboard/opensearch', function () {
        return view('pages.dashboard.opensearch.index');
    })->name('dashboard-opensearch-index');

    Route::get('/dashboard/manticore', function () {
        return view('pages.dashboard.manticore.index');
    })->name('dashboard-manticore-index');

    Route::get('/dashboard/bleve', function () {
        return view('pages.dashboard.bleve.index');
    })->name('dashboard-bleve-index');

    Route::get('/dashboard/tantivy', function () {
        return view('pages.dashboard.tantivy.index');
    })->name('dashboard-tantivy-index');

    Route::get('/dashboard/zinc', function () {
        return view('pages.dashboard.zinc.index');
    })->name('dashboard-zinc-index');

    Route::get('/dashboard/vespa', function () {
        return view('pages.dashboard.vespa.index');
    })->name('dashboard-vespa-index');

    Route::get('/dashboard/quickwit', function () {
        return view('pages.dashboard.quickwit.index');
    })->name('dashboard-quickwit-index');

    Route::get('/dashboard/lnx', function () {
        return view('pages.dashboard.lnx.index');
    })->name('dashboard-lnx-index');

    Route::get('/dashboard/toshi', function () {
        return view('pages.dashboard.toshi.index');
    })->name('dashboard-toshi-index');

    Route::get('/dashboard/sonic', function () {
        return view('pages.dashboard.sonic.index');
    })->name('dashboard-sonic-index');

    Route::get('/dashboard/redisgraph', function () {
        return view('pages.dashboard.redisgraph.index');
    })->name('dashboard-redisgraph-index');

    Route::get('/dashboard/neo4j', function () {
        return view('pages.dashboard.neo4j.index');
    })->name('dashboard-neo4j-index');

    Route::get('/dashboard/arangodb', function () {
        return view('pages.dashboard.arangodb.index');
    })->name('dashboard-arangodb-index');

    Route::get('/dashboard/orientdb', function () {
        return view('pages.dashboard.orientdb.index');
    })->name('dashboard-orientdb-index');

    Route::get('/dashboard/janusgraph', function () {
        return view('pages.dashboard.janusgraph.index');
    })->name('dashboard-janusgraph-index');

    Route::get('/dashboard/tigergraph', function () {
        return view('pages.dashboard.tigergraph.index');
    })->name('dashboard-tigergraph-index');

    Route::get('/dashboard/dgraph', function () {
        return view('pages.dashboard.dgraph.index');
    })->name('dashboard-dgraph-index');

    Route::get('/dashboard/amazon-neptune', function () {
        return view('pages.dashboard.amazon-neptune.index');
    })->name('dashboard-amazon-neptune-index');

    Route::get('/dashboard/azure-cosmos-db', function () {
        return view('pages.dashboard.azure-cosmos-db.index');
    })->name('dashboard-azure-cosmos-db-index');

    Route::get('/dashboard/google-cloud-datastore', function () {
        return view('pages.dashboard.google-cloud-datastore.index');
    })->name('dashboard-google-cloud-datastore-index');

    Route::get('/dashboard/firebase', function () {
        return view('pages.dashboard.firebase.index');
    })->name('dashboard-firebase-index');

    Route::get('/dashboard/supabase', function () {
        return view('pages.dashboard.supabase.index');
    })->name('dashboard-supabase-index');

    Route::get('/dashboard/planetscale', function () {
        return view('pages.dashboard.planetscale.index');
    })->name('dashboard-planetscale-index');

    Route::get('/dashboard/turso', function () {
        return view('pages.dashboard.turso.index');
    })->name('dashboard-turso-index');

    Route::get('/dashboard/neon', function () {
        return view('pages.dashboard.neon.index');
    })->name('dashboard-neon-index');

    Route::get('/dashboard/xata', function () {
        return view('pages.dashboard.xata.index');
    })->name('dashboard-xata-index');

    Route::get('/dashboard/fauna', function () {
        return view('pages.dashboard.fauna.index');
    })->name('dashboard-fauna-index');

    Route::get('/dashboard/upstash', function () {
        return view('pages.dashboard.upstash.index');
    })->name('dashboard-upstash-index');

    Route::get('/dashboard/railway', function () {
        return view('pages.dashboard.railway.index');
    })->name('dashboard-railway-index');

    Route::get('/dashboard/render', function () {
        return view('pages.dashboard.render.index');
    })->name('dashboard-render-index');

    Route::get('/dashboard/vercel', function () {
        return view('pages.dashboard.vercel.index');
    })->name('dashboard-vercel-index');

    Route::get('/dashboard/netlify', function () {
        return view('pages.dashboard.netlify.index');
    })->name('dashboard-netlify-index');

    Route::get('/dashboard/cloudflare', function () {
        return view('pages.dashboard.cloudflare.index');
    })->name('dashboard-cloudflare-index');

    Route::get('/dashboard/aws', function () {
        return view('pages.dashboard.aws.index');
    })->name('dashboard-aws-index');

    Route::get('/dashboard/azure', function () {
        return view('pages.dashboard.azure.index');
    })->name('dashboard-azure-index');

    Route::get('/dashboard/gcp', function () {
        return view('pages.dashboard.gcp.index');
    })->name('dashboard-gcp-index');

    Route::get('/dashboard/digitalocean', function () {
        return view('pages.dashboard.digitalocean.index');
    })->name('dashboard-digitalocean-index');

    Route::get('/dashboard/linode', function () {
        return view('pages.dashboard.linode.index');
    })->name('dashboard-linode-index');

    Route::get('/dashboard/vultr', function () {
        return view('pages.dashboard.vultr.index');
    })->name('dashboard-vultr-index');

    Route::get('/dashboard/hetzner', function () {
        return view('pages.dashboard.hetzner.index');
    })->name('dashboard-hetzner-index');

    Route::get('/dashboard/ovh', function () {
        return view('pages.dashboard.ovh.index');
    })->name('dashboard-ovh-index');

    Route::get('/dashboard/scaleway', function () {
        return view('pages.dashboard.scaleway.index');
    })->name('dashboard-scaleway-index');

    Route::get('/dashboard/alibaba', function () {
        return view('pages.dashboard.alibaba.index');
    })->name('dashboard-alibaba-index');

    Route::get('/dashboard/tencent', function () {
        return view('pages.dashboard.tencent.index');
    })->name('dashboard-tencent-index');

    Route::get('/dashboard/baidu', function () {
        return view('pages.dashboard.baidu.index');
    })->name('dashboard-baidu-index');

    Route::get('/dashboard/huawei', function () {
        return view('pages.dashboard.huawei.index');
    })->name('dashboard-huawei-index');

    Route::get('/dashboard/oracle', function () {
        return view('pages.dashboard.oracle.index');
    })->name('dashboard-oracle-index');

    Route::get('/dashboard/ibm', function () {
        return view('pages.dashboard.ibm.index');
    })->name('dashboard-ibm-index');

    Route::get('/dashboard/salesforce', function () {
        return view('pages.dashboard.salesforce.index');
    })->name('dashboard-salesforce-index');

    Route::get('/dashboard/heroku', function () {
        return view('pages.dashboard.heroku.index');
    })->name('dashboard-heroku-index');

    Route::get('/dashboard/fly', function () {
        return view('pages.dashboard.fly.index');
    })->name('dashboard-fly-index');

    Route::get('/dashboard/deno', function () {
        return view('pages.dashboard.deno.index');
    })->name('dashboard-deno-index');

    Route::get('/dashboard/bun', function () {
        return view('pages.dashboard.bun.index');
    })->name('dashboard-bun-index');

    Route::get('/dashboard/node', function () {
        return view('pages.dashboard.node.index');
    })->name('dashboard-node-index');

    Route::get('/dashboard/python', function () {
        return view('pages.dashboard.python.index');
    })->name('dashboard-python-index');

    Route::get('/dashboard/ruby', function () {
        return view('pages.dashboard.ruby.index');
    })->name('dashboard-ruby-index');

    Route::get('/dashboard/go', function () {
        return view('pages.dashboard.go.index');
    })->name('dashboard-go-index');

    Route::get('/dashboard/rust', function () {
        return view('pages.dashboard.rust.index');
    })->name('dashboard-rust-index');

    Route::get('/dashboard/java', function () {
        return view('pages.dashboard.java.index');
    })->name('dashboard-java-index');

    Route::get('/dashboard/kotlin', function () {
        return view('pages.dashboard.kotlin.index');
    })->name('dashboard-kotlin-index');

    Route::get('/dashboard/scala', function () {
        return view('pages.dashboard.scala.index');
    })->name('dashboard-scala-index');

    Route::get('/dashboard/clojure', function () {
        return view('pages.dashboard.clojure.index');
    })->name('dashboard-clojure-index');

    Route::get('/dashboard/elixir', function () {
        return view('pages.dashboard.elixir.index');
    })->name('dashboard-elixir-index');

    Route::get('/dashboard/erlang', function () {
        return view('pages.dashboard.erlang.index');
    })->name('dashboard-erlang-index');

    Route::get('/dashboard/haskell', function () {
        return view('pages.dashboard.haskell.index');
    })->name('dashboard-haskell-index');

    Route::get('/dashboard/ocaml', function () {
        return view('pages.dashboard.ocaml.index');
    })->name('dashboard-ocaml-index');

    Route::get('/dashboard/fsharp', function () {
        return view('pages.dashboard.fsharp.index');
    })->name('dashboard-fsharp-index');

    Route::get('/dashboard/csharp', function () {
        return view('pages.dashboard.csharp.index');
    })->name('dashboard-csharp-index');

    Route::get('/dashboard/cpp', function () {
        return view('pages.dashboard.cpp.index');
    })->name('dashboard-cpp-index');

    Route::get('/dashboard/c', function () {
        return view('pages.dashboard.c.index');
    })->name('dashboard-c-index');

    Route::get('/dashboard/swift', function () {
        return view('pages.dashboard.swift.index');
    })->name('dashboard-swift-index');

    Route::get('/dashboard/objective-c', function () {
        return view('pages.dashboard.objective-c.index');
    })->name('dashboard-objective-c-index');

    Route::get('/dashboard/dart', function () {
        return view('pages.dashboard.dart.index');
    })->name('dashboard-dart-index');

    Route::get('/dashboard/flutter', function () {
        return view('pages.dashboard.flutter.index');
    })->name('dashboard-flutter-index');

    Route::get('/dashboard/react', function () {
        return view('pages.dashboard.react.index');
    })->name('dashboard-react-index');

    Route::get('/dashboard/vue', function () {
        return view('pages.dashboard.vue.index');
    })->name('dashboard-vue-index');

    Route::get('/dashboard/angular', function () {
        return view('pages.dashboard.angular.index');
    })->name('dashboard-angular-index');

    Route::get('/dashboard/svelte', function () {
        return view('pages.dashboard.svelte.index');
    })->name('dashboard-svelte-index');

    Route::get('/dashboard/solid', function () {
        return view('pages.dashboard.solid.index');
    })->name('dashboard-solid-index');

    Route::get('/dashboard/qwik', function () {
        return view('pages.dashboard.qwik.index');
    })->name('dashboard-qwik-index');

    Route::get('/dashboard/preact', function () {
        return view('pages.dashboard.preact.index');
    })->name('dashboard-preact-index');

    Route::get('/dashboard/lit', function () {
        return view('pages.dashboard.lit.index');
    })->name('dashboard-lit-index');

    Route::get('/dashboard/stencil', function () {
        return view('pages.dashboard.stencil.index');
    })->name('dashboard-stencil-index');

    Route::get('/dashboard/alpine', function () {
        return view('pages.dashboard.alpine.index');
    })->name('dashboard-alpine-index');

    Route::get('/dashboard/htmx', function () {
        return view('pages.dashboard.htmx.index');
    })->name('dashboard-htmx-index');

    Route::get('/dashboard/stimulus', function () {
        return view('pages.dashboard.stimulus.index');
    })->name('dashboard-stimulus-index');

    Route::get('/dashboard/turbo', function () {
        return view('pages.dashboard.turbo.index');
    })->name('dashboard-turbo-index');

    Route::get('/dashboard/hotwire', function () {
        return view('pages.dashboard.hotwire.index');
    })->name('dashboard-hotwire-index');

    Route::get('/dashboard/livewire', function () {
        return view('pages.dashboard.livewire.index');
    })->name('dashboard-livewire-index');

    Route::get('/dashboard/inertia', function () {
        return view('pages.dashboard.inertia.index');
    })->name('dashboard-inertia-index');

    Route::get('/dashboard/jetstream', function () {
        return view('pages.dashboard.jetstream.index');
    })->name('dashboard-jetstream-index');

    Route::get('/dashboard/breeze', function () {
        return view('pages.dashboard.breeze.index');
    })->name('dashboard-breeze-index');

    Route::get('/dashboard/fortify', function () {
        return view('pages.dashboard.fortify.index');
    })->name('dashboard-fortify-index');
});

// Test route without auth
Route::get('/test-dashboard', function () {
    return 'Dashboard test route working!';
})->name('test-dashboard');

// Use the controllers
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\LegalController;

// Essential Business Pages Routes (using BusinessController)
Route::get('/about', [BusinessController::class, 'about'])->name('about');
Route::get('/pricing', [BusinessController::class, 'pricing'])->name('pricing');
Route::get('/features', [BusinessController::class, 'features'])->name('features');
Route::get('/contact', [BusinessController::class, 'contact'])->name('contact');
Route::post('/contact', [BusinessController::class, 'submitContact'])->name('contact.submit');
Route::get('/blog', [BusinessController::class, 'blog'])->name('blog');
Route::get('/case-studies', [BusinessController::class, 'caseStudies'])->name('case-studies');
Route::get('/testimonials', [BusinessController::class, 'testimonials'])->name('testimonials');
Route::get('/careers', [BusinessController::class, 'careers'])->name('careers');
Route::get('/partners', [BusinessController::class, 'partners'])->name('partners');
Route::get('/security', [BusinessController::class, 'security'])->name('security');

// Legal & Compliance Pages (using LegalController)
Route::get('/terms-of-service', [LegalController::class, 'termsOfService'])->name('terms-of-service');
Route::get('/privacy-policy', [LegalController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/cookie-policy', [LegalController::class, 'cookiePolicy'])->name('cookie-policy');
Route::get('/refund-policy', [LegalController::class, 'refundPolicy'])->name('refund-policy');
Route::get('/accessibility', [LegalController::class, 'accessibilityStatement'])->name('accessibility');

// Account Removal Page
Route::get('/account-removal', function () {
    return view('pages.account-removal');
})->name('account-removal');

// Support Page
Route::get('/support', function () {
    return view('pages.support');
})->name('support');

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');