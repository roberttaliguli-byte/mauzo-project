Put the signed Flutter release APK here with this exact filename:

    mauzosheet.apk

The landing page "Pakua App" buttons point to:

    https://www.mauzosheetai.co.tz/download/mauzosheet.apk

Build the APK from the Flutter project with:

    flutter build apk --release

Then copy:

    build\app\outputs\flutter-apk\app-release.apk

to:

    public\downloads\mauzosheet.apk

If the file is missing, the download route returns a 404 with a Swahili
message instead of a broken download.
