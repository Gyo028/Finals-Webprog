const passport = require('passport');
const GoogleStrategy = require('passport-google-oauth20').Strategy;

passport.use(new GoogleStrategy({
    clientID: process.env.GOOGLE_CLIENT_ID,
    clientSecret: process.env.GOOGLE_CLIENT_SECRET,
    callbackURL: "/auth/google/callback"
  },
  (accessToken, refreshToken, profile, done) => {
    // This is where you'd save the user to your database
    // For now, we just pass the profile along
    return done(null, profile);
  }
));

// These allow the user to stay logged in via sessions
passport.serializeUser((user, done) => done(null, user));
passport.deserializeUser((user, done) => done(null, user));