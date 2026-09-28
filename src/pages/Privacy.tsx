import { LegalPage } from './Legal';
import { PRIVACY } from '../data/pages';

export function Privacy() {
  return <LegalPage content={PRIVACY} path="/privacy-policy" crumbLabel="Privacy policy" />;
}
