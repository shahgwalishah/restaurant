import ApplicationLogo from '@/Components/ApplicationLogo';
import { Link } from '@inertiajs/react';

export default function GuestLayout({ children }) {
    return (
        <div className="min-h-screen bg-[#f6f6f1] lg:grid lg:grid-cols-[1.08fr_0.92fr]">
            <section className="relative hidden overflow-hidden bg-[#1f2824] p-12 text-white lg:flex lg:flex-col lg:justify-between">
                <div className="absolute -left-32 top-1/4 h-80 w-80 rounded-full border border-white/5" />
                <div className="absolute -left-16 top-1/3 h-80 w-80 rounded-full border border-[#e75b3d]/20" />

                <Link href="/" className="relative flex items-center gap-4">
                    <ApplicationLogo className="h-20 w-20 object-contain" />
                    <div className="grid">
                        <strong className="text-xl font-bold tracking-tight">
                            Multan Bites
                        </strong>
                        <span className="text-xs uppercase tracking-[0.22em] text-white/45">
                            Restaurant Manager
                        </span>
                    </div>
                </Link>

                <div className="relative max-w-xl">
                    <span className="mb-5 inline-flex rounded-full border border-[#e75b3d]/30 bg-[#e75b3d]/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-[#f28a70]">
                        Built for Multan
                    </span>
                    <h1 className="text-5xl font-bold leading-[1.08] tracking-tight">
                        Har order, har table,
                        <span className="block text-[#e75b3d]">
                            aapke control mein.
                        </span>
                    </h1>
                    <p className="mt-6 max-w-md text-base leading-7 text-white/55">
                        Sales, kitchen, inventory aur customers — sab kuch ek
                        simple aur reliable system mein.
                    </p>
                </div>

                <p className="relative text-xs text-white/30">
                    Proudly designed for Pakistan&apos;s restaurant teams.
                </p>
            </section>

            <div className="flex min-h-screen items-center justify-center px-5 py-10 sm:px-10">
                <div className="w-full max-w-md">
                    <Link
                        href="/"
                        className="mb-8 flex items-center justify-center gap-3 lg:hidden"
                    >
                        <ApplicationLogo className="h-20 w-20 object-contain" />
                        <div className="grid">
                            <strong className="text-lg font-bold text-[#1f2824]">
                                Multan Bites
                            </strong>
                            <span className="text-[10px] uppercase tracking-[0.2em] text-gray-400">
                                Restaurant Manager
                            </span>
                        </div>
                    </Link>

                    <div className="overflow-hidden rounded-2xl border border-[#e7e6df] bg-white px-7 py-8 shadow-[0_22px_70px_rgba(31,40,36,0.08)] sm:px-10 sm:py-10">
                        {children}
                    </div>
                </div>
            </div>
        </div>
    );
}
